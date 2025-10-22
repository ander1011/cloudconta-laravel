<?php

namespace App\Http\Controllers;

use App\Models\EmpresaObrigacao;
use App\Models\Empresa;
use App\Models\LayoutDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ObrigacaoFiscalController extends Controller
{
    /**
     * Listar obrigações de uma empresa
     */
    public function index($empresaId)
    {
        try {
            $empresa = Empresa::findOrFail($empresaId);
            
            if ($empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $obrigacoes = EmpresaObrigacao::with('layoutDocumento')
                ->where('empresa_id', $empresaId)
                ->orderBy('ativo', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $obrigacoes->map(function($obrigacao) {
                    return [
                        'id' => $obrigacao->id,
                        'layout_documento_id' => $obrigacao->layout_documento_id,
                        'layout_nome' => $obrigacao->layoutDocumento->nome,
                        'layout_codigo' => $obrigacao->layoutDocumento->codigo,
                        'layout_tipo' => $obrigacao->layoutDocumento->tipo,
                        'dia_vencimento' => $obrigacao->getDiaVencimento(),
                        'dia_vencimento_customizado' => $obrigacao->dia_vencimento_customizado,
                        'data_inicio' => $obrigacao->data_inicio?->format('d/m/Y'),
                        'data_fim' => $obrigacao->data_fim?->format('d/m/Y'),
                        'vigente' => $obrigacao->estaVigente(),
                        'observacoes' => $obrigacao->observacoes,
                        'ativo' => $obrigacao->ativo
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar obrigações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar layouts disponíveis para vincular
     */
    public function layoutsDisponiveis($empresaId)
    {
        try {
            $empresa = Empresa::findOrFail($empresaId);
            
            if ($empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Buscar layouts que ainda não foram vinculados
            $layoutsVinculados = EmpresaObrigacao::where('empresa_id', $empresaId)
                ->pluck('layout_documento_id')
                ->toArray();

            $layoutsDisponiveis = LayoutDocumento::where('ativo', true)
                ->whereNotIn('id', $layoutsVinculados)
                ->orderBy('tipo')
                ->orderBy('nome')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $layoutsDisponiveis->map(function($layout) {
                    return [
                        'id' => $layout->id,
                        'nome' => $layout->nome,
                        'codigo' => $layout->codigo,
                        'tipo' => $layout->tipo,
                        'periodicidade' => $layout->periodicidade,
                        'dia_vencimento' => $layout->dia_vencimento,
                        'enviar_cliente' => $layout->enviar_cliente
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar layouts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vincular obrigação à empresa
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'empresa_id' => 'required|exists:empresas,id',
                'layout_documento_id' => 'required|exists:layouts_documentos,id',
                'dia_vencimento_customizado' => 'nullable|integer|min:1|max:31',
                'data_inicio' => 'nullable|date',
                'data_fim' => 'nullable|date|after_or_equal:data_inicio',
                'observacoes' => 'nullable|string',
                'ativo' => 'boolean',
                'gerar_periodos_ano' => 'nullable|integer|min:2020|max:2030'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $empresa = Empresa::findOrFail($request->empresa_id);
            if ($empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Verificar se já existe vínculo
            $vinculoExistente = EmpresaObrigacao::where('empresa_id', $request->empresa_id)
                ->where('layout_documento_id', $request->layout_documento_id)
                ->first();

            if ($vinculoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta obrigação já está vinculada à empresa'
                ], 422);
            }

            DB::beginTransaction();

            $obrigacao = EmpresaObrigacao::create([
                'empresa_id' => $request->empresa_id,
                'layout_documento_id' => $request->layout_documento_id,
                'dia_vencimento_customizado' => $request->dia_vencimento_customizado,
                'data_inicio' => $request->data_inicio,
                'data_fim' => $request->data_fim,
                'observacoes' => $request->observacoes,
                'ativo' => $request->ativo ?? true
            ]);

            // Gerar períodos fiscais se solicitado
            if ($request->gerar_periodos_ano) {
                $periodos = $obrigacao->gerarPeriodos($request->gerar_periodos_ano);
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Obrigação vinculada com sucesso! ' . count($periodos) . ' períodos gerados.',
                    'data' => $obrigacao->load('layoutDocumento'),
                    'periodos_gerados' => count($periodos)
                ], 201);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Obrigação vinculada com sucesso',
                'data' => $obrigacao->load('layoutDocumento')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao vincular obrigação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar vínculo
     */
    public function update(Request $request, $id)
    {
        try {
            $obrigacao = EmpresaObrigacao::with('empresa')->findOrFail($id);
            
            if ($obrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'dia_vencimento_customizado' => 'nullable|integer|min:1|max:31',
                'data_inicio' => 'nullable|date',
                'data_fim' => 'nullable|date|after_or_equal:data_inicio',
                'observacoes' => 'nullable|string',
                'ativo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $obrigacao->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Vínculo atualizado com sucesso',
                'data' => $obrigacao->load('layoutDocumento')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar vínculo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover vínculo
     */
    public function destroy($id)
    {
        try {
            $obrigacao = EmpresaObrigacao::with(['empresa', 'periodosFiscais'])->findOrFail($id);
            
            if ($obrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Verificar se há períodos vinculados
            $totalPeriodos = $obrigacao->periodosFiscais()->count();

            if ($totalPeriodos > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Não é possível remover. Existem {$totalPeriodos} períodos fiscais vinculados a esta obrigação."
                ], 422);
            }

            $obrigacao->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vínculo removido com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover vínculo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar períodos fiscais para um ano
     */
    public function gerarPeriodos(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ano' => 'required|integer|min:2020|max:2030'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ano inválido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $obrigacao = EmpresaObrigacao::with('empresa')->findOrFail($id);
            
            if ($obrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $periodos = $obrigacao->gerarPeriodos($request->ano);

            return response()->json([
                'success' => true,
                'message' => count($periodos) . ' períodos gerados com sucesso',
                'data' => [
                    'ano' => $request->ano,
                    'periodos_gerados' => count($periodos),
                    'periodos' => $periodos
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar períodos: ' . $e->getMessage()
            ], 500);
        }
    }
}
