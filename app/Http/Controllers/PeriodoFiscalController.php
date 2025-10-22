<?php

namespace App\Http\Controllers;

use App\Models\PeriodoFiscal;
use App\Models\EmpresaObrigacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PeriodoFiscalController extends Controller
{
    /**
     * Dashboard de períodos - vencimentos hoje, próximos, atrasados
     */
    public function dashboard()
    {
        try {
            $usuarioId = auth()->id();

            // Vencimentos hoje
            $vencidosHoje = PeriodoFiscal::with(['empresaObrigacao.empresa', 'empresaObrigacao.layoutDocumento'])
                ->whereHas('empresaObrigacao.empresa', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })
                ->vencidosHoje()
                ->get();

            // Próximos 7 dias
            $proximosVencimentos = PeriodoFiscal::with(['empresaObrigacao.empresa', 'empresaObrigacao.layoutDocumento'])
                ->whereHas('empresaObrigacao.empresa', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })
                ->vencidosProximos(7)
                ->get();

            // Atrasados
            $atrasados = PeriodoFiscal::with(['empresaObrigacao.empresa', 'empresaObrigacao.layoutDocumento'])
                ->whereHas('empresaObrigacao.empresa', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })
                ->atrasados()
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'vencidos_hoje' => $vencidosHoje->map(fn($p) => $this->formatarPeriodo($p)),
                    'proximos_vencimentos' => $proximosVencimentos->map(fn($p) => $this->formatarPeriodo($p)),
                    'atrasados' => $atrasados->map(fn($p) => $this->formatarPeriodo($p)),
                    'totais' => [
                        'hoje' => $vencidosHoje->count(),
                        'proximos' => $proximosVencimentos->count(),
                        'atrasados' => $atrasados->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar períodos com filtros
     */
    public function index(Request $request)
    {
        try {
            $usuarioId = auth()->id();
            
            $query = PeriodoFiscal::with(['empresaObrigacao.empresa', 'empresaObrigacao.layoutDocumento'])
                ->whereHas('empresaObrigacao.empresa', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                });

            // Filtros
            if ($request->empresa_id) {
                $query->whereHas('empresaObrigacao', function($q) use ($request) {
                    $q->where('empresa_id', $request->empresa_id);
                });
            }

            if ($request->mes) {
                $query->where('mes_competencia', $request->mes);
            }

            if ($request->ano) {
                $query->where('ano_competencia', $request->ano);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $periodos = $query->orderBy('data_vencimento_ajustada')
                             ->orderBy('mes_competencia')
                             ->get();

            return response()->json([
                'success' => true,
                'data' => $periodos->map(fn($p) => $this->formatarPeriodo($p))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar períodos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar período específico
     */
    public function show($id)
    {
        try {
            $periodo = PeriodoFiscal::with(['empresaObrigacao.empresa', 'empresaObrigacao.layoutDocumento'])
                ->findOrFail($id);
            
            // Verificar acesso
            if ($periodo->empresaObrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatarPeriodo($periodo)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar período: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar período
     */
    public function update(Request $request, $id)
    {
        try {
            $periodo = PeriodoFiscal::with('empresaObrigacao.empresa')->findOrFail($id);
            
            if ($periodo->empresaObrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|in:aguardando,documentos_recebidos,calculado,enviado,pago,atrasado,dispensado',
                'valor_calculado' => 'nullable|numeric|min:0',
                'valor_pago' => 'nullable|numeric|min:0',
                'data_pagamento' => 'nullable|date',
                'numero_guia' => 'nullable|string|max:100',
                'codigo_barras' => 'nullable|string|max:200',
                'observacoes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $periodo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Período atualizado com sucesso',
                'data' => $this->formatarPeriodo($periodo->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar período: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar como pago
     */
    public function marcarPago(Request $request, $id)
    {
        try {
            $periodo = PeriodoFiscal::with('empresaObrigacao.empresa')->findOrFail($id);
            
            if ($periodo->empresaObrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'valor_pago' => 'required|numeric|min:0',
                'data_pagamento' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $periodo->marcarComoPago(
                $request->valor_pago,
                $request->data_pagamento ? Carbon::parse($request->data_pagamento) : null
            );

            return response()->json([
                'success' => true,
                'message' => 'Período marcado como pago',
                'data' => $this->formatarPeriodo($periodo->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar como dispensado/sem movimento
     */
    public function marcarDispensado(Request $request, $id)
    {
        try {
            $periodo = PeriodoFiscal::with('empresaObrigacao.empresa')->findOrFail($id);
            
            if ($periodo->empresaObrigacao->empresa->usuario_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            $periodo->marcarComoDispensado($request->motivo);

            return response()->json([
                'success' => true,
                'message' => 'Período marcado como dispensado/sem movimento',
                'data' => $this->formatarPeriodo($periodo->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como dispensado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calendário de vencimentos
     */
    public function calendario(Request $request)
    {
        try {
            $mes = $request->mes ?? now()->month;
            $ano = $request->ano ?? now()->year;
            $usuarioId = auth()->id();

            $periodos = PeriodoFiscal::with(['empresaObrigacao.empresa', 'empresaObrigacao.layoutDocumento'])
                ->whereHas('empresaObrigacao.empresa', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })
                ->whereYear('data_vencimento_ajustada', $ano)
                ->whereMonth('data_vencimento_ajustada', $mes)
                ->orderBy('data_vencimento_ajustada')
                ->get();

            // Agrupar por dia
            $calendario = [];
            foreach ($periodos as $periodo) {
                $dia = $periodo->data_vencimento_ajustada->day;
                
                if (!isset($calendario[$dia])) {
                    $calendario[$dia] = [];
                }
                
                $calendario[$dia][] = $this->formatarPeriodo($periodo);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'mes' => $mes,
                    'ano' => $ano,
                    'calendario' => $calendario,
                    'total_periodos' => $periodos->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar calendário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formatar período para resposta
     */
    private function formatarPeriodo($periodo)
    {
        return [
            'id' => $periodo->id,
            'empresa_id' => $periodo->empresaObrigacao->empresa_id,
            'empresa_nome' => $periodo->empresaObrigacao->empresa->nome,
            'empresa_cnpj' => $periodo->empresaObrigacao->empresa->cnpj_formatado,
            'layout_nome' => $periodo->empresaObrigacao->layoutDocumento->nome,
            'layout_codigo' => $periodo->empresaObrigacao->layoutDocumento->codigo,
            'layout_tipo' => $periodo->empresaObrigacao->layoutDocumento->tipo,
            'mes_competencia' => $periodo->mes_competencia,
            'ano_competencia' => $periodo->ano_competencia,
            'competencia_formatada' => $periodo->competencia_formatada,
            'data_vencimento_original' => $periodo->data_vencimento_original->format('d/m/Y'),
            'data_vencimento_ajustada' => $periodo->data_vencimento_ajustada->format('d/m/Y'),
            'foi_antecipado' => $periodo->foi_antecipado,
            'foi_postergado' => $periodo->foi_postergado,
            'motivo_ajuste' => $periodo->motivo_ajuste,
            'status' => $periodo->status,
            'status_badge' => $periodo->status_badge,
            'valor_calculado' => $periodo->valor_calculado,
            'valor_calculado_formatado' => $periodo->valor_calculado_formatado,
            'valor_pago' => $periodo->valor_pago,
            'valor_pago_formatado' => $periodo->valor_pago_formatado,
            'data_pagamento' => $periodo->data_pagamento?->format('d/m/Y'),
            'numero_guia' => $periodo->numero_guia,
            'codigo_barras' => $periodo->codigo_barras,
            'observacoes' => $periodo->observacoes,
            'esta_atrasado' => $periodo->estaAtrasado(),
            'vence_hoje' => $periodo->venceHoje()
        ];
    }
}
