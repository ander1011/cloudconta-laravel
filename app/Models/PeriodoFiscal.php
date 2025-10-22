<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoFiscal extends Model
{
    protected $table = 'periodos_fiscais';
    
    protected $fillable = [
        'empresa_obrigacao_id',
        'mes_competencia',
        'ano_competencia',
        'data_vencimento_original',
        'data_vencimento_ajustada',
        'foi_antecipado',
        'foi_postergado',
        'motivo_ajuste',
        'status',
        'valor_calculado',
        'valor_pago',
        'data_pagamento',
        'numero_guia',
        'codigo_barras',
        'observacoes'
    ];

    protected $casts = [
        'mes_competencia' => 'integer',
        'ano_competencia' => 'integer',
        'data_vencimento_original' => 'date',
        'data_vencimento_ajustada' => 'date',
        'foi_antecipado' => 'boolean',
        'foi_postergado' => 'boolean',
        'valor_calculado' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_pagamento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status disponíveis
    const STATUS_AGUARDANDO = 'aguardando';
    const STATUS_DOCUMENTOS_RECEBIDOS = 'documentos_recebidos';
    const STATUS_CALCULADO = 'calculado';
    const STATUS_ENVIADO = 'enviado';
    const STATUS_PAGO = 'pago';
    const STATUS_ATRASADO = 'atrasado';
    const STATUS_DISPENSADO = 'dispensado';

    // Relacionamento com empresa_obrigacao
    public function empresaObrigacao()
    {
        return $this->belongsTo(EmpresaObrigacao::class, 'empresa_obrigacao_id');
    }

    // Relacionamento com empresa (através de empresa_obrigacao)
    public function empresa()
    {
        return $this->hasOneThrough(
            Empresa::class,
            EmpresaObrigacao::class,
            'id',
            'id',
            'empresa_obrigacao_id',
            'empresa_id'
        );
    }

    // Relacionamento com layout documento (através de empresa_obrigacao)
    public function layoutDocumento()
    {
        return $this->hasOneThrough(
            LayoutDocumento::class,
            EmpresaObrigacao::class,
            'id',
            'id',
            'empresa_obrigacao_id',
            'layout_documento_id'
        );
    }

    // Scopes
    public function scopeAguardando($query)
    {
        return $query->where('status', self::STATUS_AGUARDANDO);
    }

    public function scopeVencidosHoje($query)
    {
        return $query->where('data_vencimento_ajustada', now()->toDateString())
                     ->whereIn('status', [self::STATUS_AGUARDANDO, self::STATUS_DOCUMENTOS_RECEBIDOS]);
    }

    public function scopeVencidosProximos($query, $dias = 7)
    {
        return $query->whereBetween('data_vencimento_ajustada', [
                        now()->toDateString(),
                        now()->addDays($dias)->toDateString()
                    ])
                     ->whereIn('status', [self::STATUS_AGUARDANDO, self::STATUS_DOCUMENTOS_RECEBIDOS]);
    }

    public function scopeAtrasados($query)
    {
        return $query->where('data_vencimento_ajustada', '<', now()->toDateString())
                     ->whereIn('status', [self::STATUS_AGUARDANDO, self::STATUS_DOCUMENTOS_RECEBIDOS]);
    }

    public function scopeCompetencia($query, $mes, $ano)
    {
        return $query->where('mes_competencia', $mes)
                     ->where('ano_competencia', $ano);
    }

    // Accessor para competência formatada
    public function getCompetenciaFormatadaAttribute()
    {
        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        return ($meses[$this->mes_competencia] ?? $this->mes_competencia) . '/' . $this->ano_competencia;
    }

    // Accessor para status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aguardando' => '<span class="badge bg-warning">Aguardando</span>',
            'documentos_recebidos' => '<span class="badge bg-info">Documentos Recebidos</span>',
            'calculado' => '<span class="badge bg-primary">Calculado</span>',
            'enviado' => '<span class="badge bg-success">Enviado</span>',
            'pago' => '<span class="badge bg-success">Pago</span>',
            'atrasado' => '<span class="badge bg-danger">Atrasado</span>',
            'dispensado' => '<span class="badge bg-secondary">Dispensado</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Accessor para valor calculado formatado
    public function getValorCalculadoFormatadoAttribute()
    {
        return $this->valor_calculado ? 'R$ ' . number_format($this->valor_calculado, 2, ',', '.') : 'R$ 0,00';
    }

    // Accessor para valor pago formatado
    public function getValorPagoFormatadoAttribute()
    {
        return $this->valor_pago ? 'R$ ' . number_format($this->valor_pago, 2, ',', '.') : 'R$ 0,00';
    }

    // Verifica se está atrasado
    public function estaAtrasado()
    {
        return $this->data_vencimento_ajustada->lt(now()) &&
               in_array($this->status, [self::STATUS_AGUARDANDO, self::STATUS_DOCUMENTOS_RECEBIDOS]);
    }

    // Verifica se vence hoje
    public function venceHoje()
    {
        return $this->data_vencimento_ajustada->isToday();
    }

    // Marcar como pago
    public function marcarComoPago($valorPago, $dataPagamento = null)
    {
        $this->update([
            'status' => self::STATUS_PAGO,
            'valor_pago' => $valorPago,
            'data_pagamento' => $dataPagamento ?? now()
        ]);
    }

    // Marcar como dispensado
    public function marcarComoDispensado($motivo = null)
    {
        $this->update([
            'status' => self::STATUS_DISPENSADO,
            'observacoes' => $motivo
        ]);
    }
}
