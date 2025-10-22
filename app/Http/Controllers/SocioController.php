<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocioController extends Controller
{
    /**
     * Listar todos os sócios de uma empresa
     */
    public function index($empresaId)
    {
        try {
            $empresa = Empresa::findOrFail($empresaId);
            
            // Verificar se usuário tem acesso à empresa
            if ($empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $socios = Socio::where('empresa_id', $empresaId)
                          ->orderBy('nome_socio')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $socios->map(function($socio) {
                    return [
                        'id' => $socio->id,
                        'nome_socio' => $socio->nome_socio,
                        'qualificacao_socio' => $socio->qualificacao_socio,
                        'qualificacao_formatada' => $socio->qualificacao_formatada,
                        'cpf_cnpj_socio' => $socio->cpf_cnpj_socio,
                        'cpf_cnpj_formatado' => $socio->cpf_cnpj_formatado,
                        'percentual_capital' => $socio->percentual_capital,
                        'percentual_formatado' => $socio->percentual_formatado,
                        'faixa_etaria' => $socio->faixa_etaria,
                        'pais' => $socio->pais,
                        'data_entrada_sociedade' => $socio->data_entrada_sociedade?->format('d/m/Y')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar sócios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar um sócio específico
     */
    public function show($id)
    {
        try {
            $socio = Socio::with('empresa')->findOrFail($id);
            
            // Verificar se usuário tem acesso
            if ($socio->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $socio->id,
                    'empresa_id' => $socio->empresa_id,
                    'nome_socio' => $socio->nome_socio,
                    'qualificacao_socio' => $socio->qualificacao_socio,
                    'qualificacao_formatada' => $socio->qualificacao_formatada,
                    'cpf_cnpj_socio' => $socio->cpf_cnpj_socio,
                    'cpf_cnpj_formatado' => $socio->cpf_cnpj_formatado,
                    'percentual_capital' => $socio->percentual_capital,
                    'percentual_formatado' => $socio->percentual_formatado,
                    'faixa_etaria' => $socio->faixa_etaria,
                    'pais' => $socio->pais,
                    'data_entrada_sociedade' => $socio->data_entrada_sociedade?->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar sócio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo sócio
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'empresa_id' => 'required|exists:empresas,id',
                'nome_socio' => 'required|string|max:255',
                'qualificacao_socio' => 'nullable|string|max:100',
                'cpf_cnpj_socio' => 'nullable|string|max:18',
                'percentual_capital' => 'nullable|numeric|min:0|max:100',
                'faixa_etaria' => 'nullable|string|max:50',
                'pais' => 'nullable|string|max:100',
                'data_entrada_sociedade' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar se usuário tem acesso à empresa
            $empresa = Empresa::findOrFail($request->empresa_id);
            if ($empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $socio = Socio::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Sócio cadastrado com sucesso',
                'data' => $socio
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar sócio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar sócio
     */
    public function update(Request $request, $id)
    {
        try {
            $socio = Socio::with('empresa')->findOrFail($id);
            
            // Verificar se usuário tem acesso
            if ($socio->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nome_socio' => 'sometimes|required|string|max:255',
                'qualificacao_socio' => 'nullable|string|max:100',
                'cpf_cnpj_socio' => 'nullable|string|max:18',
                'percentual_capital' => 'nullable|numeric|min:0|max:100',
                'faixa_etaria' => 'nullable|string|max:50',
                'pais' => 'nullable|string|max:100',
                'data_entrada_sociedade' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $socio->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Sócio atualizado com sucesso',
                'data' => $socio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar sócio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar sócio
     */
    public function destroy($id)
    {
        try {
            $socio = Socio::with('empresa')->findOrFail($id);
            
            // Verificar se usuário tem acesso
            if ($socio->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $socio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sócio removido com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover sócio: ' . $e->getMessage()
            ], 500);
        }
    }
}
