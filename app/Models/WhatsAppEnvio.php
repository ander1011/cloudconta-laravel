<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppEnvio extends Model
{
    protected $table = 'whatsapp_envios';
    
    public $timestamps = false; // Só tem created_at
    
    protected $fillable = [
        'entrega_id',
        'contato_id',
        'whatsapp_destino',
        'mensagem',
        'status',
        'message_id',
        'arquivos_enviados',
        'erro',
        'tempo_resposta'
    ];

    protected $casts = [
        'arquivos_enviados' => 'array',
        'tempo_resposta' => 'integer',
        'created_at' => 'datetime'
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    // Status disponíveis
    const STATUS_ENVIADO = 'enviado';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_LIDO = 'lido';
    const STATUS_ERRO = 'erro';

    // Relacionamento com entrega
    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    // Relacionamento com contato
    public function contato()
    {
        return $this->belongsTo(ContatoWhatsApp::class, 'contato_id');
    }

    // Scopes
    public function scopeEnviado($query)
    {
        return $query->where('status', self::STATUS_ENVIADO);
    }

    public function scopeEntregue($query)
    {
        return $query->where('status', self::STATUS_ENTREGUE);
    }

    public function scopeLido($query)
    {
        return $query->where('status', self::STATUS_LIDO);
    }

    public function scopeComErro($query)
    {
        return $query->where('status', self::STATUS_ERRO);
    }

    public function scopeDaEntrega($query, $entregaId)
    {
        return $query->where('entrega_id', $entregaId);
    }

    public function scopeDoContato($query, $contatoId)
    {
        return $query->where('contato_id', $contatoId);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // Accessor para status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'enviado' => '<span class="badge bg-info"><i class="fas fa-paper-plane"></i> Enviado</span>',
            'entregue' => '<span class="badge bg-success"><i class="fas fa-check"></i> Entregue</span>',
            'lido' => '<span class="badge bg-primary"><i class="fas fa-check-double"></i> Lido</span>',
            'erro' => '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Erro</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Accessor para ícone do status
    public function getStatusIconAttribute()
    {
        $icons = [
            'enviado' => '<i class="fas fa-paper-plane text-info"></i>',
            'entregue' => '<i class="fas fa-check text-success"></i>',
            'lido' => '<i class="fas fa-check-double text-primary"></i>',
            'erro' => '<i class="fas fa-exclamation-triangle text-danger"></i>'
        ];

        return $icons[$this->status] ?? '<i class="fas fa-question-circle"></i>';
    }

    // Accessor para WhatsApp formatado
    public function getWhatsappDestinoFormatadoAttribute()
    {
        $numero = preg_replace('/\D/', '', $this->whatsapp_destino);
        
        if (strlen($numero) == 13) {
            return preg_replace('/(\d{2})(\d{2})(\d{5})(\d{4})/', '+$1 ($2) $3-$4', $numero);
        }
        
        return $this->whatsapp_destino;
    }

    // Accessor para tempo de resposta formatado
    public function getTempoRespostaFormatadoAttribute()
    {
        if (!$this->tempo_resposta) return 'N/A';
        
        if ($this->tempo_resposta < 1000) {
            return $this->tempo_resposta . ' ms';
        }
        
        return round($this->tempo_resposta / 1000, 2) . ' s';
    }

    // Accessor para link WhatsApp
    public function getWhatsappLinkAttribute()
    {
        $numero = preg_replace('/\D/', '', $this->whatsapp_destino);
        return "https://wa.me/{$numero}";
    }

    // Verificar se foi bem-sucedido
    public function foiBemSucedido()
    {
        return in_array($this->status, [self::STATUS_ENVIADO, self::STATUS_ENTREGUE, self::STATUS_LIDO]);
    }

    // Contar arquivos enviados
    public function getTotalArquivosAttribute()
    {
        return is_array($this->arquivos_enviados) ? count($this->arquivos_enviados) : 0;
    }
}
