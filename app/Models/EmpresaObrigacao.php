<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaObrigacao extends Model
{
    protected $table = 'empresas_obrigacoes';
    
    protected $fillable = [
        'empresa_id',
        'layout_documento_id',
        'dia_vencimento_customizado',
        'data_inicio',
        'data_fim',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'dia_vencimento_customizado' => 'integer',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com layout de documento (obrigação fiscal)
    public function layoutDocumento()
    {
        return $this->belongsTo(LayoutDocumento::class, 'layout_documento_id');
    }

    // Relacionamento com períodos fiscais
    public function periodosFiscais()
    {
        return $this->hasMany(PeriodoFiscal::class, 'empresa_obrigacao_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeVigentes($query, $data = null)
    {
        $data = $data ?? now();
        
        return $query->where(function($q) use ($data) {
            $q->whereNull('data_inicio')
              ->orWhere('data_inicio', '<=', $data);
        })->where(function($q) use ($data) {
            $q->whereNull('data_fim')
              ->orWhere('data_fim', '>=', $data);
        });
    }

    // Verifica se está vigente em uma data
    public function estaVigente($data = null)
    {
        $data = $data ?? now();

        $inicioOk = !$this->data_inicio || $this->data_inicio->lte($data);
        $fimOk = !$this->data_fim || $this->data_fim->gte($data);

        return $inicioOk && $fimOk;
    }

    // Obter dia de vencimento (customizado ou padrão do layout)
    public function getDiaVencimento()
    {
        return $this->dia_vencimento_customizado ?? $this->layoutDocumento->dia_vencimento;
    }

    // Gerar períodos fiscais para um ano
    public function gerarPeriodos($ano)
    {
        $periodos = [];
        $layout = $this->layoutDocumento;

        for ($mes = 1; $mes <= 12; $mes++) {
            // Verificar se o layout se aplica a este mês
            if (!$layout->seAplicaAoMes($mes)) {
                continue;
            }

            // Verificar se está vigente neste mês
            $dataMes = \Carbon\Carbon::createFromDate($ano, $mes, 1);
            if (!$this->estaVigente($dataMes)) {
                continue;
            }

            // Calcular vencimento
            $vencimentoOriginal = $layout->calcularVencimento($mes, $ano);
            $vencimentoAjustado = $vencimentoOriginal; // TODO: aplicar regras de feriados

            $periodo = $this->periodosFiscais()->firstOrCreate([
                'mes_competencia' => $mes,
                'ano_competencia' => $ano
            ], [
                'data_vencimento_original' => $vencimentoOriginal,
                'data_vencimento_ajustada' => $vencimentoAjustado,
                'status' => 'aguardando'
            ]);

            $periodos[] = $periodo;
        }

        return $periodos;
    }
}
