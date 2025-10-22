<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $table = 'entregas';
    
    protected $fillable = [
        'empresa_id',
        'competencia',
        'status',
        'total_documentos',
        'documentos_recebidos',
        'data_completude',
        'data_envio',
        'enviado_por',
        'observacoes'
    ];

    protected $casts = [
        'total_documentos' => 'integer',
        'documentos_recebidos' => 'integer',
        'data_completude' => 'datetime',
        'data_envio' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status disponíveis
    const STATUS_AGUARDANDO = 'aguardando';
    const STATUS_COMPLETO = 'completo';
    const STATUS_ENVIADO = 'enviado';

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com usuário que enviou
    public function enviadoPor()
    {
        return $this->belongsTo(User::class, 'enviado_por');
    }

    // Relacionamento com itens da entrega
    public function itens()
    {
        return $this->hasMany(EntregaItem::class, 'entrega_id');
    }

    // Relacionamento com envios WhatsApp
    public function enviosWhatsApp()
    {
        return $this->hasMany(WhatsAppEnvio::class, 'entrega_id');
    }

    // Scopes
    public function scopeAguardando($query)
    {
        return $query->where('status', self::STATUS_AGUARDANDO);
    }

    public function scopeCompleto($query)
    {
        return $query->where('status', self::STATUS_COMPLETO);
    }

    public function scopeEnviado($query)
    {
        return $query->where('status', self::STATUS_ENVIADO);
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorCompetencia($query, $competencia)
    {
        return $query->where('competencia', $competencia);
    }

    public function scopeCompetenciaMes($query, $mes, $ano)
    {
        $competencia = sprintf('%04d-%02d', $ano, $mes);
        return $query->where('competencia', $competencia);
    }

    // Accessor para competência formatada
    public function getCompetenciaFormatadaAttribute()
    {
        if (!$this->competencia) return '';
        
        [$ano, $mes] = explode('-', $this->competencia);
        $meses = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        
        return ($meses[$mes] ?? $mes) . '/' . $ano;
    }

    // Accessor para porcentagem de completude
    public function getPorcentagemAttribute()
    {
        if ($this->total_documentos == 0) return 0;
        return round(($this->documentos_recebidos / $this->total_documentos) * 100);
    }

    // Accessor para status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aguardando' => '<span class="badge bg-warning">Aguardando</span>',
            'completo' => '<span class="badge bg-success">Completo</span>',
            'enviado' => '<span class="badge bg-primary">Enviado</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Verifica se a entrega está completa
    public function estaCompleta()
    {
        return $this->total_documentos > 0 && 
               $this->documentos_recebidos >= $this->total_documentos;
    }

    // Atualizar contador de documentos
    public function atualizarContadores()
    {
        $this->total_documentos = $this->itens()->count();
        $this->documentos_recebidos = $this->itens()
            ->whereIn('status', ['recebido', 'sem_movimento'])
            ->count();
        
        // Se completou agora, marcar data
        if ($this->estaCompleta() && !$this->data_completude) {
            $this->data_completude = now();
            $this->status = self::STATUS_COMPLETO;
        }
        
        $this->save();
    }
}
