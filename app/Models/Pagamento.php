<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'pagamentos';
    
    protected $fillable = [
        'empresa_id',
        'modulo_id',
        'valor',
        'status',
        'metodo_pagamento',
        'referencia_externa',
        'data_pagamento',
        'observacoes'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status disponíveis
    const STATUS_PENDENTE = 'pendente';
    const STATUS_PAGO = 'pago';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_ESTORNADO = 'estornado';

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com módulo
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    // Scopes
    public function scopePendentes($query)
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    public function scopePagos($query)
    {
        return $query->where('status', self::STATUS_PAGO);
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Accessor para valor formatado
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    // Accessor para status formatado com cor
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pendente' => '<span class="badge bg-warning">Pendente</span>',
            'pago' => '<span class="badge bg-success">Pago</span>',
            'cancelado' => '<span class="badge bg-danger">Cancelado</span>',
            'estornado' => '<span class="badge bg-secondary">Estornado</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Métodos para alterar status
    public function marcarComoPago()
    {
        $this->update([
            'status' => self::STATUS_PAGO,
            'data_pagamento' => now()
        ]);
    }

    public function cancelar()
    {
        $this->update([
            'status' => self::STATUS_CANCELADO
        ]);
    }

    public function estornar()
    {
        $this->update([
            'status' => self::STATUS_ESTORNADO
        ]);
    }
}
