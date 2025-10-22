<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContatoWhatsApp extends Model
{
    protected $table = 'contatos_whatsapp';
    
    protected $fillable = [
        'empresa_id',
        'nome',
        'whatsapp',
        'email',
        'cargo',
        'tipo',
        'recebe_notificacoes',
        'ativo',
        'ultima_interacao',
        'observacoes'
    ];

    protected $casts = [
        'recebe_notificacoes' => 'boolean',
        'ativo' => 'boolean',
        'ultima_interacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Tipos disponíveis
    const TIPO_CONTADOR = 'contador';
    const TIPO_RESPONSAVEL = 'responsavel';
    const TIPO_FINANCEIRO = 'financeiro';
    const TIPO_SOCIO = 'socio';
    const TIPO_OUTRO = 'outro';

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com envios WhatsApp
    public function enviosWhatsApp()
    {
        return $this->hasMany(WhatsAppEnvio::class, 'contato_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeRecebeNotificacoes($query)
    {
        return $query->where('recebe_notificacoes', true);
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Accessor para WhatsApp formatado
    public function getWhatsappFormatadoAttribute()
    {
        $numero = preg_replace('/\D/', '', $this->whatsapp);
        
        if (strlen($numero) == 13) {
            // Formato: 55 51 99999-9999
            return preg_replace('/(\d{2})(\d{2})(\d{5})(\d{4})/', '+$1 ($2) $3-$4', $numero);
        }
        
        return $this->whatsapp;
    }

    // Accessor para tipo formatado
    public function getTipoFormatadoAttribute()
    {
        $tipos = [
            'contador' => 'Contador',
            'responsavel' => 'Responsável',
            'financeiro' => 'Financeiro',
            'socio' => 'Sócio',
            'outro' => 'Outro'
        ];

        return $tipos[$this->tipo] ?? 'Desconhecido';
    }

    // Accessor para badge de tipo
    public function getTipoBadgeAttribute()
    {
        $badges = [
            'contador' => '<span class="badge bg-primary">Contador</span>',
            'responsavel' => '<span class="badge bg-success">Responsável</span>',
            'financeiro' => '<span class="badge bg-warning">Financeiro</span>',
            'socio' => '<span class="badge bg-info">Sócio</span>',
            'outro' => '<span class="badge bg-secondary">Outro</span>'
        ];

        return $badges[$this->tipo] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Accessor para link WhatsApp Web
    public function getWhatsappLinkAttribute()
    {
        $numero = preg_replace('/\D/', '', $this->whatsapp);
        return "https://wa.me/{$numero}";
    }

    // Atualizar última interação
    public function atualizarUltimaInteracao()
    {
        $this->update(['ultima_interacao' => now()]);
    }

    // Contar envios realizados
    public function getTotalEnviosAttribute()
    {
        return $this->enviosWhatsApp()->count();
    }

    // Contar envios bem-sucedidos
    public function getEnviosSucessoAttribute()
    {
        return $this->enviosWhatsApp()
            ->whereIn('status', ['enviado', 'entregue', 'lido'])
            ->count();
    }
}
