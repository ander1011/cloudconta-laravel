<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\LayoutDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentTemplateController extends Controller
{
    /**
     * Listar todos os templates
     */
    public function index(Request $request)
    {
        try {
            $query = DocumentTemplate::with('layoutDocumento');

            // Filtros
            if ($request->layout_documento_id) {
                $query->where('layout_documento_id', $request->layout_documento_id);
            }

            if ($request->ativo !== null) {
                $query->where('ativo', $request->ativo);
            }

            if ($request->teste_aprovado !== null) {
                $query->where('teste_aprovado', $request->teste_aprovado);
            }

            $templates = $query->ordenadosPorPrioridade()->get();

            return response()->json([
                'success' => true,
                'data' => $templates->map(function($template) {
                    return [
                        'id' => $template->id,
                        'layout_documento_id' => $template->layout_documento_id,
                        'layout_nome' => $template->layoutDocumento->nome,
                        'nome' => $template->nome,
                        'descricao' => $template->descricao,
                        'tipo_extracao' => $template->tipo_extracao,
                        'tipo_extracao_formatado' => $template->tipo_extracao_formatado,
                        'prioridade' => $template->prioridade,
                        'ativo' => $template->ativo,
                        'teste_aprovado' => $template->teste_aprovado,
                        'status_teste_badge' => $template->status_teste_badge,
                        'ultima_validacao' => $template->ultima_validacao?->format('d/m/Y H:i')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar templates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar template específico
     */
    public function show($id)
    {
        try {
            $template = DocumentTemplate::with('layoutDocumento')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $template->id,
                    'layout_documento_id' => $template->layout_documento_id,
                    'layout_nome' => $template->layoutDocumento->nome,
                    'nome' => $template->nome,
                    'descricao' => $template->descricao,
                    'padrao_nome_arquivo' => $template->padrao_nome_arquivo,
                    'palavras_chave' => $template->palavras_chave,
                    'tipo_extracao' => $template->tipo_extracao,
                    'padrao_cnpj' => $template->padrao_cnpj,
                    'padrao_periodo' => $template->padrao_periodo,
                    'padrao_valor' => $template->padrao_valor,
                    'padrao_vencimento' => $template->padrao_vencimento,
                    'coordenadas_cnpj' => $template->coordenadas_cnpj,
                    'coordenadas_periodo' => $template->coordenadas_periodo,
                    'coordenadas_valor' => $template->coordenadas_valor,
                    'campos_extracao' => $template->campos_extracao,
                    'prioridade' => $template->prioridade,
                    'ativo' => $template->ativo,
                    'exemplo_arquivo' => $template->exemplo_arquivo,
                    'teste_aprovado' => $template->teste_aprovado,
                    'ultima_validacao' => $template->ultima_validacao?->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo template
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'layout_documento_id' => 'required|exists:layouts_documentos,id',
                'nome' => 'required|string|max:200',
                'descricao' => 'nullable|string',
                'padrao_nome_arquivo' => 'nullable|string|max:500',
                'palavras_chave' => 'nullable|array',
                'tipo_extracao' => 'required|in:regex,ocr,posicao,misto',
                'padrao_cnpj' => 'nullable|string|max:500',
                'padrao_periodo' => 'nullable|string|max:500',
                'padrao_valor' => 'nullable|string|max:500',
                'padrao_vencimento' => 'nullable|string|max:500',
                'coordenadas_cnpj' => 'nullable|array',
                'coordenadas_periodo' => 'nullable|array',
                'coordenadas_valor' => 'nullable|array',
                'campos_extracao' => 'nullable|array',
                'prioridade' => 'nullable|integer|min:0',
                'ativo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $template = DocumentTemplate::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Template criado com sucesso',
                'data' => $template->load('layoutDocumento')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar template
     */
    public function update(Request $request, $id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nome' => 'sometimes|required|string|max:200',
                'descricao' => 'nullable|string',
                'padrao_nome_arquivo' => 'nullable|string|max:500',
                'palavras_chave' => 'nullable|array',
                'tipo_extracao' => 'sometimes|required|in:regex,ocr,posicao,misto',
                'padrao_cnpj' => 'nullable|string|max:500',
                'padrao_periodo' => 'nullable|string|max:500',
                'padrao_valor' => 'nullable|string|max:500',
                'padrao_vencimento' => 'nullable|string|max:500',
                'coordenadas_cnpj' => 'nullable|array',
                'coordenadas_periodo' => 'nullable|array',
                'coordenadas_valor' => 'nullable|array',
                'campos_extracao' => 'nullable|array',
                'prioridade' => 'nullable|integer|min:0',
                'ativo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $template->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Template atualizado com sucesso',
                'data' => $template->load('layoutDocumento')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar template
     */
    public function destroy($id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template removido com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Testar template com arquivo de exemplo
     */
    public function testar(Request $request, $id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'texto_extraido' => 'required|string',
                'nome_arquivo' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Testar correspondência por nome de arquivo
            $correspondeArquivo = false;
            if ($request->nome_arquivo) {
                $correspondeArquivo = $template->correspondeAoArquivo($request->nome_arquivo);
            }

            // Testar correspondência por texto
            $correspondeTexto = $template->correspondeAoTexto($request->texto_extraido);

            // Extrair dados
            $dadosExtraidos = [
                'cnpj' => $template->extrairCnpj($request->texto_extraido),
                'periodo' => $template->extrairPeriodo($request->texto_extraido),
                'valor' => $template->extrairValor($request->texto_extraido)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'template_id' => $template->id,
                    'template_nome' => $template->nome,
                    'corresponde_arquivo' => $correspondeArquivo,
                    'corresponde_texto' => $correspondeTexto,
                    'dados_extraidos' => $dadosExtraidos,
                    'sucesso_extracao' => !empty($dadosExtraidos['cnpj']) || !empty($dadosExtraidos['periodo'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprovar teste do template
     */
    public function aprovarTeste($id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            $template->aprovarTeste();

            return response()->json([
                'success' => true,
                'message' => 'Template aprovado com sucesso',
                'data' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reprovar teste do template
     */
    public function reprovarTeste($id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            $template->reprovarTeste();

            return response()->json([
                'success' => true,
                'message' => 'Template reprovado',
                'data' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reprovar template: ' . $e->getMessage()
            ], 500);
        }
    }
}
