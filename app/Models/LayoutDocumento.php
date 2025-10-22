<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayoutDocumento extends Model
{
    protected $table = 'layouts_documentos';
    
    protected $fillable = [
        'empresa_id',
        'nome',
        'codigo',
        'tipo',
        'periodicidade',
        'dia_vencimento',
        'obrigatorio',
        'ativo',
        'observacoes',
        'meses_aplicaveis',
        'antecipa_feriado',
        'posterga_feriado',
        'antecipa_fim_semana',
        'posterga_fim_semana',
        'considera_dia_util',
        'orgao_responsavel',
        'link_informacoes',
        'nome_pasta',
        'enviar_cliente',
        'tipo_vencimento',
        'meses_apos_competencia',
        'dia_util_ordem',
        'aplicavel_a'
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
        'ativo' => 'boolean',
        'dia_vencimento' => 'integer',
        'meses_aplicaveis' => 'array',
        'antecipa_feriado' => 'boolean',
        'posterga_feriado' => 'boolean',
        'antecipa_fim_semana' => 'boolean',
        'posterga_fim_semana' => 'boolean',
        'considera_dia_util' => 'boolean',
        'enviar_cliente' => 'boolean',
        'meses_apos_competencia' => 'integer',
        'dia_util_ordem' => 'integer',
        'aplicavel_a' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Tipos disponíveis
    const TIPO_FEDERAL = 'federal';
    const TIPO_ESTADUAL = 'estadual';
    const TIPO_MUNICIPAL = 'municipal';
    const TIPO_TRABALHISTA = 'trabalhista';
    const TIPO_OUTRO = 'outro';

    // Periodicidades disponíveis
    const PERIODICIDADE_MENSAL = 'mensal';
    const PERIODICIDADE_TRIMESTRAL = 'trimestral';
    const PERIODICIDADE_SEMESTRAL = 'semestral';
    const PERIODICIDADE_ANUAL = 'anual';

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com itens de entrega
    public function entregasItens()
    {
        return $this->hasMany(EntregaItem::class, 'layout_documento_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Accessor para tipo formatado
    public function getTipoFormatadoAttribute()
    {
        $tipos = [
            'federal' => 'Federal',
            'estadual' => 'Estadual',
            'municipal' => 'Municipal',
            'trabalhista' => 'Trabalhista',
            'outro' => 'Outro'
        ];

        return $tipos[$this->tipo] ?? 'Desconhecido';
    }

    // Accessor para badge de tipo
    public function getTipoBadgeAttribute()
    {
        $badges = [
            'federal' => '<span class="badge bg-primary">Federal</span>',
            'estadual' => '<span class="badge bg-success">Estadual</span>',
            'municipal' => '<span class="badge bg-info">Municipal</span>',
            'trabalhista' => '<span class="badge bg-warning">Trabalhista</span>',
            'outro' => '<span class="badge bg-secondary">Outro</span>'
        ];

        return $badges[$this->tipo] ?? '<span class="badge bg-dark">Desconhecido</span>';
    }

    // Relacionamento com templates de documentos
    public function templates()
    {
        return $this->hasMany(DocumentTemplate::class, 'layout_documento_id');
    }

    // Relacionamento com templates ativos
    public function templatesAtivos()
    {
        return $this->templates()->where('ativo', true)->orderBy('prioridade', 'desc');
    }

    // Verifica se o imposto se aplica a um determinado mês
    public function seAplicaAoMes($mes)
    {
        if (empty($this->meses_aplicaveis)) return true; // NULL = todos os meses
        
        return in_array($mes, $this->meses_aplicaveis);
    }

    // Verifica se deve enviar ao cliente
    public function deveEnviarCliente()
    {
        return $this->enviar_cliente === true;
    }

    // Accessor para órgão responsável ou padrão
    public function getOrgaoResponsavelOuPadraoAttribute()
    {
        if ($this->orgao_responsavel) return $this->orgao_responsavel;

        $orgaos = [
            'federal' => 'Receita Federal do Brasil',
            'estadual' => 'Secretaria da Fazenda Estadual',
            'municipal' => 'Secretaria Municipal de Fazenda',
            'trabalhista' => 'Ministério do Trabalho'
        ];

        return $orgaos[$this->tipo] ?? 'Não informado';
    }

    // Accessor para tipo de vencimento formatado
    public function getTipoVencimentoFormatadoAttribute()
    {
        $tipos = [
            'dia_fixo' => 'Dia fixo do mês',
            'dia_mes_subsequente' => 'Dia do mês subsequente',
            'dia_util_mes_subsequente' => 'Dia útil do mês subsequente'
        ];

        return $tipos[$this->tipo_vencimento] ?? 'Dia fixo do mês';
    }

    // Calcular data de vencimento para uma competência
    public function calcularVencimento($mes, $ano)
    {
        // Lógica simplificada - expandir com regras de feriados/fins de semana depois
        $mesVencimento = $mes + $this->meses_apos_competencia;
        $anoVencimento = $ano;

        if ($mesVencimento > 12) {
            $anoVencimento++;
            $mesVencimento -= 12;
        }

        $dia = $this->dia_vencimento;
        
        try {
            $vencimento = \Carbon\Carbon::createFromDate($anoVencimento, $mesVencimento, $dia);
            
            // Aplicar regras de fim de semana
            if ($this->antecipa_fim_semana && $vencimento->isWeekend()) {
                while ($vencimento->isWeekend()) {
                    $vencimento->subDay();
                }
            } elseif ($this->posterga_fim_semana && $vencimento->isWeekend()) {
                while ($vencimento->isWeekend()) {
                    $vencimento->addDay();
                }
            }

            return $vencimento;
        } catch (\Exception $e) {
            // Dia inválido para o mês (ex: 31 de fevereiro)
            return \Carbon\Carbon::createFromDate($anoVencimento, $mesVencimento, 1)->endOfMonth();
        }
    }
}
