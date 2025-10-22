<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaStatusLog extends Model
{
    protected $table = 'entregas_status_log';
    
    public $timestamps = false; // Só tem created_at
    
    protected $fillable = [
        'entrega_item_id',
        'status_anterior',
        'status_novo',
        'usuario_id',
        'ip_address',
        'user_agent',
        'observacao'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    // Relacionamento com item da entrega
    public function entregaItem()
    {
        return $this->belongsTo(EntregaItem::class, 'entrega_item_id');
    }

    // Relacionamento com usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopeDoItem($query, $itemId)
    {
        return $query->where('entrega_item_id', $itemId);
    }

    public function scopeDoUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // Accessor para status anterior formatado
    public function getStatusAnteriorFormatadoAttribute()
    {
        $status = [
            'aguardando' => 'Aguardando',
            'recebido' => 'Recebido',
            'sem_movimento' => 'Sem Movimento'
        ];

        return $status[$this->status_anterior] ?? 'N/A';
    }

    // Accessor para status novo formatado
    public function getStatusNovoFormatadoAttribute()
    {
        $status = [
            'aguardando' => 'Aguardando',
            'recebido' => 'Recebido',
            'sem_movimento' => 'Sem Movimento'
        ];

        return $status[$this->status_novo] ?? 'Desconhecido';
    }

    // Accessor para descrição da mudança
    public function getDescricaoMudancaAttribute()
    {
        return sprintf(
            'De "%s" para "%s"',
            $this->status_anterior_formatado,
            $this->status_novo_formatado
        );
    }
}
