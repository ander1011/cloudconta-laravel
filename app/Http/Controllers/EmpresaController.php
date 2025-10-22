<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::doUsuario()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('workspace.empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('workspace.empresas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cnpj' => 'required|string|min:14|unique:empresas,cnpj',
            'nome' => 'required|string|max:255',
        ]);

        $cnpj = preg_replace('/\D/', '', $request->cnpj);

        try {
            // Consultar dados na BrasilAPI
            $dadosReceita = $this->buscarDadosCNPJ($cnpj);
            
            $empresa = Empresa::create([
                'usuario_id' => auth()->id(),
                'nome' => $request->nome,
                'razao_social' => $dadosReceita['razao_social'] ?? null,
                'nome_fantasia' => $dadosReceita['nome_fantasia'] ?? null,
                'cnpj' => $cnpj,
                'inscricao_estadual' => $dadosReceita['inscricao_estadual'] ?? null,
                'regime_tributario' => $this->identificarRegimeTributario($dadosReceita),
                'situacao_cadastral' => $dadosReceita['descricao_situacao_cadastral'] ?? null,
                'capital_social' => $dadosReceita['capital_social'] ?? 0,
                'porte' => $dadosReceita['porte'] ?? null,
                'natureza_juridica' => $dadosReceita['natureza_juridica'] ?? null,
                'cnae_principal' => $dadosReceita['cnae_fiscal'] ?? null,
                'cnae_descricao' => $dadosReceita['cnae_fiscal_descricao'] ?? null,
                'cnae_fiscal' => $dadosReceita['cnae_fiscal'] ?? null,
                'cnae_fiscal_descricao' => $dadosReceita['cnae_fiscal_descricao'] ?? null,
                'cnaes_secundarios' => $dadosReceita['cnaes_secundarios'] ?? null,
                'email' => $request->email ?? $dadosReceita['email'] ?? null,
                'telefone' => $request->telefone ?? $dadosReceita['ddd_telefone_1'] ?? null,
                'endereco' => $this->formatarEndereco($dadosReceita),
                'logradouro' => $dadosReceita['logradouro'] ?? null,
                'numero' => $dadosReceita['numero'] ?? null,
                'complemento' => $dadosReceita['complemento'] ?? null,
                'bairro' => $dadosReceita['bairro'] ?? null,
                'municipio' => $dadosReceita['municipio'] ?? null,
                'uf' => $dadosReceita['uf'] ?? null,
                'cep' => $dadosReceita['cep'] ?? null,
                'data_inicio_atividade' => $dadosReceita['data_inicio_atividade'] ?? null,
                'data_situacao_cadastral' => $dadosReceita['data_situacao_cadastral'] ?? null,
                'dados_brasilapi_atualizados_em' => now(),
                'ativo' => true
            ]);

            // Salvar sócios (QSA)
            if (!empty($dadosReceita['qsa'])) {
                foreach ($dadosReceita['qsa'] as $socioData) {
                    $empresa->socios()->create([
                        'nome_socio' => $socioData['nome_socio'] ?? null,
                        'qualificacao_socio' => $socioData['qualificacao_socio'] ?? null,
                        'data_entrada_sociedade' => $socioData['data_entrada_sociedade'] ?? null,
                        'faixa_etaria' => $socioData['faixa_etaria'] ?? null,
                        'cpf_cnpj_socio' => $socioData['cpf_cnpj_socio'] ?? null,
                        'percentual_capital' => $socioData['percentual_capital_social'] ?? null,
                        'pais' => $socioData['pais'] ?? 'Brasil'
                    ]);
                }
            }

            return redirect()
                ->route('workspace.empresas.index')
                ->with('success', 'Empresa cadastrada com sucesso! Dados atualizados da Receita Federal.');

        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar empresa: ' . $e->getMessage());
            
            // Salvar sem dados da Receita se API falhar
            $empresa = Empresa::create([
                'usuario_id' => auth()->id(),
                'nome' => $request->nome,
                'cnpj' => $cnpj,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'endereco' => $request->endereco,
                'ativo' => true
            ]);

            return redirect()
                ->route('workspace.empresas.index')
                ->with('warning', 'Empresa cadastrada, mas não foi possível consultar dados na Receita Federal.');
        }
    }

    public function show(Empresa $empresa)
    {
        $this->authorize('view', $empresa);
        return view('workspace.empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $this->authorize('update', $empresa);
        return view('workspace.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $this->authorize('update', $empresa);

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string',
            'endereco' => 'nullable|string'
        ]);

        $empresa->update($request->only([
            'nome', 'email', 'telefone', 'endereco'
        ]));

        return redirect()
            ->route('workspace.empresas.index')
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        $this->authorize('delete', $empresa);
        
        $empresa->delete();

        return redirect()
            ->route('workspace.empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }

    public function consultarCnpj(Request $request)
    {
        $cnpj = preg_replace('/\D/', '', $request->cnpj);
        
        if (strlen($cnpj) !== 14) {
            return response()->json([
                'success' => false,
                'message' => 'CNPJ deve ter 14 dígitos'
            ], 400);
        }

        try {
            $dados = $this->buscarDadosCNPJ($cnpj);
            return response()->json([
                'success' => true,
                'dados' => $dados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CNPJ: ' . $e->getMessage()
            ], 500);
        }
    }

    private function buscarDadosCNPJ($cnpj)
    {
        $url = "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}";
        
        $response = Http::timeout(15)->get($url);
        
        if (!$response->successful()) {
            throw new \Exception('CNPJ não encontrado ou API indisponível');
        }

        $data = $response->json();
        
        if (isset($data['message']) && strpos($data['message'], 'CNPJ') !== false) {
            throw new \Exception($data['message']);
        }

        return $data;
    }

    private function formatarEndereco($dados)
    {
        if (empty($dados)) return null;

        $endereco = '';
        if (!empty($dados['logradouro'])) $endereco .= $dados['logradouro'];
        if (!empty($dados['numero'])) $endereco .= ', ' . $dados['numero'];
        if (empty($dados['numero']) && !empty($endereco)) $endereco .= ', S/N';
        if (!empty($dados['bairro'])) $endereco .= ', ' . $dados['bairro'];
        if (!empty($dados['municipio'])) $endereco .= ', ' . $dados['municipio'];
        if (!empty($dados['uf'])) $endereco .= ' - ' . $dados['uf'];
        if (!empty($dados['cep'])) {
            $cep = preg_replace('/(\d{5})(\d{3})/', '$1-$2', $dados['cep']);
            $endereco .= ', CEP: ' . $cep;
        }

        return $endereco ?: null;
    }

    /**
     * Identificar regime tributário baseado em porte e natureza jurídica
     */
    private function identificarRegimeTributario($dados)
    {
        // MEI
        if (isset($dados['porte']) && strtoupper($dados['porte']) === 'MEI') {
            return 'mei';
        }

        // Simples Nacional (baseado em porte)
        if (isset($dados['porte']) && in_array(strtoupper($dados['porte']), ['MICRO EMPRESA', 'EMPRESA DE PEQUENO PORTE'])) {
            return 'simples_nacional';
        }

        // Retornar null para definir manualmente depois
        return null;
    }

    /**
     * API: Atualizar dados da empresa com BrasilAPI
     */
    public function atualizarDadosBrasilApi(Empresa $empresa)
    {
        try {
            $this->authorize('update', $empresa);

            $cnpj = preg_replace('/\D/', '', $empresa->cnpj);
            $dadosReceita = $this->buscarDadosCNPJ($cnpj);

            // Atualizar dados da empresa
            $empresa->update([
                'razao_social' => $dadosReceita['razao_social'] ?? $empresa->razao_social,
                'nome_fantasia' => $dadosReceita['nome_fantasia'] ?? $empresa->nome_fantasia,
                'inscricao_estadual' => $dadosReceita['inscricao_estadual'] ?? $empresa->inscricao_estadual,
                'regime_tributario' => $this->identificarRegimeTributario($dadosReceita) ?? $empresa->regime_tributario,
                'situacao_cadastral' => $dadosReceita['descricao_situacao_cadastral'] ?? $empresa->situacao_cadastral,
                'capital_social' => $dadosReceita['capital_social'] ?? $empresa->capital_social,
                'porte' => $dadosReceita['porte'] ?? $empresa->porte,
                'natureza_juridica' => $dadosReceita['natureza_juridica'] ?? $empresa->natureza_juridica,
                'cnae_principal' => $dadosReceita['cnae_fiscal'] ?? $empresa->cnae_principal,
                'cnae_descricao' => $dadosReceita['cnae_fiscal_descricao'] ?? $empresa->cnae_descricao,
                'cnae_fiscal' => $dadosReceita['cnae_fiscal'] ?? $empresa->cnae_fiscal,
                'cnae_fiscal_descricao' => $dadosReceita['cnae_fiscal_descricao'] ?? $empresa->cnae_fiscal_descricao,
                'cnaes_secundarios' => $dadosReceita['cnaes_secundarios'] ?? $empresa->cnaes_secundarios,
                'logradouro' => $dadosReceita['logradouro'] ?? $empresa->logradouro,
                'numero' => $dadosReceita['numero'] ?? $empresa->numero,
                'complemento' => $dadosReceita['complemento'] ?? $empresa->complemento,
                'bairro' => $dadosReceita['bairro'] ?? $empresa->bairro,
                'municipio' => $dadosReceita['municipio'] ?? $empresa->municipio,
                'uf' => $dadosReceita['uf'] ?? $empresa->uf,
                'cep' => $dadosReceita['cep'] ?? $empresa->cep,
                'data_inicio_atividade' => $dadosReceita['data_inicio_atividade'] ?? $empresa->data_inicio_atividade,
                'data_situacao_cadastral' => $dadosReceita['data_situacao_cadastral'] ?? $empresa->data_situacao_cadastral,
                'dados_brasilapi_atualizados_em' => now()
            ]);

            // Atualizar sócios (limpar e recriar)
            if (!empty($dadosReceita['qsa'])) {
                $empresa->socios()->delete(); // Limpar sócios antigos
                
                foreach ($dadosReceita['qsa'] as $socioData) {
                    $empresa->socios()->create([
                        'nome_socio' => $socioData['nome_socio'] ?? null,
                        'qualificacao_socio' => $socioData['qualificacao_socio'] ?? null,
                        'data_entrada_sociedade' => $socioData['data_entrada_sociedade'] ?? null,
                        'faixa_etaria' => $socioData['faixa_etaria'] ?? null,
                        'cpf_cnpj_socio' => $socioData['cpf_cnpj_socio'] ?? null,
                        'percentual_capital' => $socioData['percentual_capital_social'] ?? null,
                        'pais' => $socioData['pais'] ?? 'Brasil'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Dados atualizados com sucesso da Receita Federal',
                'data' => $empresa->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Configurar pasta de backup customizada
     */
    public function configurarPastaBackup(Request $request, Empresa $empresa)
    {
        try {
            $this->authorize('update', $empresa);

            $request->validate([
                'pasta_backup' => 'nullable|string|max:500'
            ]);

            $empresa->update([
                'pasta_backup' => $request->pasta_backup
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pasta de backup configurada com sucesso',
                'data' => [
                    'pasta_backup' => $empresa->pasta_backup,
                    'pasta_backup_efetiva' => $empresa->getPastaBackup()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao configurar pasta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obter detalhes completos da empresa
     */
    public function detalhesCompletos(Empresa $empresa)
    {
        try {
            $this->authorize('view', $empresa);

            $empresa->load(['socios', 'obrigacoes.layoutDocumento', 'contatosWhatsApp']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $empresa->id,
                    'nome' => $empresa->nome,
                    'razao_social' => $empresa->razao_social,
                    'nome_fantasia' => $empresa->nome_fantasia,
                    'cnpj' => $empresa->cnpj,
                    'cnpj_formatado' => $empresa->cnpj_formatado,
                    'inscricao_estadual' => $empresa->inscricao_estadual,
                    'regime_tributario' => $empresa->regime_tributario,
                    'regime_tributario_formatado' => $empresa->regime_tributario_formatado,
                    'situacao_cadastral' => $empresa->situacao_cadastral,
                    'capital_social' => $empresa->capital_social,
                    'capital_social_formatado' => $empresa->capital_social_formatado,
                    'porte' => $empresa->porte,
                    'porte_formatado' => $empresa->porte_formatado,
                    'natureza_juridica' => $empresa->natureza_juridica,
                    'cnae_principal' => $empresa->cnae_principal,
                    'cnae_descricao' => $empresa->cnae_descricao,
                    'email' => $empresa->email,
                    'telefone' => $empresa->telefone,
                    'telefone_formatado' => $empresa->telefone_formatado,
                    'endereco_completo' => $empresa->endereco_completo,
                    'logradouro' => $empresa->logradouro,
                    'numero' => $empresa->numero,
                    'complemento' => $empresa->complemento,
                    'bairro' => $empresa->bairro,
                    'municipio' => $empresa->municipio,
                    'uf' => $empresa->uf,
                    'cep' => $empresa->cep,
                    'cep_formatado' => $empresa->cep_formatado,
                    'data_inicio_atividade' => $empresa->data_inicio_atividade?->format('d/m/Y'),
                    'data_situacao_cadastral' => $empresa->data_situacao_cadastral?->format('d/m/Y'),
                    'dados_brasilapi_atualizados_em' => $empresa->dados_brasilapi_atualizados_em?->format('d/m/Y H:i'),
                    'dados_brasilapi_desatualizados' => $empresa->dadosBrasilApiDesatualizados(),
                    'pasta_backup' => $empresa->pasta_backup,
                    'pasta_backup_efetiva' => $empresa->getPastaBackup(),
                    'ativo' => $empresa->ativo,
                    'socios' => $empresa->socios,
                    'obrigacoes' => $empresa->obrigacoes,
                    'contatos_whatsapp' => $empresa->contatosWhatsApp
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar detalhes: ' . $e->getMessage()
            ], 500);
        }
    }
}
