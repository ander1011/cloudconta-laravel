<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaItem extends Model
{
    protected $table = 'entregas_itens';
    
    protected $fillable = [
        'entrega_id',
        'layout_documento_id',
        'status',
        'arquivo_path',
        'arquivo_nome',
        'arquivo_url',
        'data_recebimento',
        'cnpj_extraido',
        'ie_extraida',
        'marcado_sem_movimento_por',
        'marcado_sem_movimento_em',
        'observacoes'
    ];

    protected $casts = [
        'data_recebimento' => 'datetime',
        'marcado_sem_movimento_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status disponíveis
    const STATUS_AGUARDANDO = 'aguardando';
    const STATUS_RECEBIDO = 'recebido';
    const STATUS_SEM_MOVIMENTO = 'sem_movimento';

    // Relacionamento com entrega
    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    // Relacionamento com layout documento
    public function layoutDocumento()
    {
        return $this->belongsTo(LayoutDocumento::class, 'layout_documento_id');
    }

    // Relacionamento com usuário que marcou sem movimento
    public function marcadoPor()
    {
        return $this->belongsTo(User::class, 'marcado_sem_movimento_por');
    }

    // Relacionamento com log de status
    public function statusLog()
    {
        return $this->hasMany(EntregaStatusLog::class, 'entrega_item_id');
    }

    // Scopes
    public function scopeAguardando($query)
    {
        return $query->where('status', self::STATUS_AGUARDANDO);
    }

    public function scopeRecebido($query)
    {
        return $query->where('status', self::STATUS_RECEBIDO);
    }

    public function scopeSemMovimento($query)
    {
        return $query->where('status', self::STATUS_SEM_MOVIMENTO);
    }

    public function scopeDaEntrega($query, $entregaId)
    {
        return $query->where('entrega_id', $entregaId);
    }

    // Accessor para status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aguardando' => '<span class="badge bg-warning"><i class="fas fa-clock"></i> Aguardando</span>',
            'recebido' => '<span class="badge bg-success"><i class="fas fa-check"></i> Recebido</span>',
            'sem_movimento' => '<span class="badge bg-secondary"><i class="fas fa-minus"></i> Sem Movimento</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Accessor para ícone do status
    public function getStatusIconAttribute()
    {
        $icons = [
            'aguardando' => '<i class="fas fa-clock text-warning"></i>',
            'recebido' => '<i class="fas fa-check-circle text-success"></i>',
            'sem_movimento' => '<i class="fas fa-minus-circle text-secondary"></i>'
        ];

        return $icons[$this->status] ?? '<i class="fas fa-question-circle"></i>';
    }

    // Marcar como recebido
    public function marcarComoRecebido($arquivoPath, $arquivoNome, $arquivoUrl = null, $cnpj = null, $ie = null)
    {
        $statusAnterior = $this->status;
        
        $this->update([
            'status' => self::STATUS_RECEBIDO,
            'arquivo_path' => $arquivoPath,
            'arquivo_nome' => $arquivoNome,
            'arquivo_url' => $arquivoUrl,
            'data_recebimento' => now(),
            'cnpj_extraido' => $cnpj,
            'ie_extraida' => $ie
        ]);

        // Registrar no log
        $this->registrarMudancaStatus($statusAnterior, self::STATUS_RECEBIDO);

        // Atualizar contadores da entrega
        $this->entrega->atualizarContadores();
    }

    // Marcar como sem movimento
    public function marcarComoSemMovimento($usuarioId = null, $observacao = null)
    {
        $statusAnterior = $this->status;
        
        $this->update([
            'status' => self::STATUS_SEM_MOVIMENTO,
            'marcado_sem_movimento_por' => $usuarioId ?? auth()->id(),
            'marcado_sem_movimento_em' => now(),
            'observacoes' => $observacao
        ]);

        // Registrar no log
        $this->registrarMudancaStatus($statusAnterior, self::STATUS_SEM_MOVIMENTO, $observacao);

        // Atualizar contadores da entrega
        $this->entrega->atualizarContadores();
    }

    // Registrar mudança de status no log
    private function registrarMudancaStatus($statusAnterior, $statusNovo, $observacao = null)
    {
        EntregaStatusLog::create([
            'entrega_item_id' => $this->id,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'usuario_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'observacao' => $observacao
        ]);
    }
}
