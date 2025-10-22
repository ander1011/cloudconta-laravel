<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $table = 'document_templates';
    
    protected $fillable = [
        'layout_documento_id',
        'nome',
        'descricao',
        'padrao_nome_arquivo',
        'palavras_chave',
        'tipo_extracao',
        'padrao_cnpj',
        'padrao_periodo',
        'padrao_valor',
        'padrao_vencimento',
        'coordenadas_cnpj',
        'coordenadas_periodo',
        'coordenadas_valor',
        'campos_extracao',
        'prioridade',
        'ativo',
        'exemplo_arquivo',
        'teste_aprovado',
        'ultima_validacao'
    ];

    protected $casts = [
        'palavras_chave' => 'array',
        'coordenadas_cnpj' => 'array',
        'coordenadas_periodo' => 'array',
        'coordenadas_valor' => 'array',
        'campos_extracao' => 'array',
        'prioridade' => 'integer',
        'ativo' => 'boolean',
        'teste_aprovado' => 'boolean',
        'ultima_validacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Tipos de extração disponíveis
    const TIPO_REGEX = 'regex';
    const TIPO_OCR = 'ocr';
    const TIPO_POSICAO = 'posicao';
    const TIPO_MISTO = 'misto';

    // Relacionamento com layout documento
    public function layoutDocumento()
    {
        return $this->belongsTo(LayoutDocumento::class, 'layout_documento_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeTestadosAprovados($query)
    {
        return $query->where('teste_aprovado', true);
    }

    public function scopeOrdenadosPorPrioridade($query)
    {
        return $query->orderBy('prioridade', 'desc');
    }

    public function scopeDoLayout($query, $layoutId)
    {
        return $query->where('layout_documento_id', $layoutId);
    }

    // Verifica se o template corresponde ao nome do arquivo
    public function correspondeAoArquivo($nomeArquivo)
    {
        if (empty($this->padrao_nome_arquivo)) {
            return false;
        }

        // Usar regex para verificar correspondência
        return preg_match('/' . $this->padrao_nome_arquivo . '/i', $nomeArquivo);
    }

    // Verifica se o template corresponde ao texto extraído
    public function correspondeAoTexto($textoExtraido)
    {
        if (empty($this->palavras_chave)) {
            return false;
        }

        $encontradas = 0;
        foreach ($this->palavras_chave as $palavra) {
            if (stripos($textoExtraido, $palavra) !== false) {
                $encontradas++;
            }
        }

        // Considerar match se encontrou 50% ou mais das palavras-chave
        return $encontradas >= (count($this->palavras_chave) * 0.5);
    }

    // Extrair CNPJ do texto usando o padrão
    public function extrairCnpj($texto)
    {
        if (empty($this->padrao_cnpj)) {
            return null;
        }

        if (preg_match('/' . $this->padrao_cnpj . '/', $texto, $matches)) {
            $cnpj = preg_replace('/\D/', '', $matches[0]);
            return strlen($cnpj) == 14 ? $cnpj : null;
        }

        return null;
    }

    // Extrair período/competência do texto
    public function extrairPeriodo($texto)
    {
        if (empty($this->padrao_periodo)) {
            return null;
        }

        if (preg_match('/' . $this->padrao_periodo . '/', $texto, $matches)) {
            return $this->parsearPeriodo($matches[0]);
        }

        return null;
    }

    // Extrair valor do texto
    public function extrairValor($texto)
    {
        if (empty($this->padrao_valor)) {
            return null;
        }

        if (preg_match('/' . $this->padrao_valor . '/', $texto, $matches)) {
            // Limpar e converter para decimal
            $valor = preg_replace('/[^\d,.]/', '', $matches[0]);
            $valor = str_replace(['.', ','], ['', '.'], $valor);
            return floatval($valor);
        }

        return null;
    }

    // Parsear string de período para array [mes, ano]
    private function parsearPeriodo($stringPeriodo)
    {
        // Tentar diversos formatos comuns
        
        // Formato: MM/YYYY ou MM-YYYY
        if (preg_match('/(\d{2})[\/\-](\d{4})/', $stringPeriodo, $matches)) {
            return [
                'mes' => intval($matches[1]),
                'ano' => intval($matches[2])
            ];
        }

        // Formato: MMYYYY
        if (preg_match('/(\d{2})(\d{4})/', $stringPeriodo, $matches)) {
            return [
                'mes' => intval($matches[1]),
                'ano' => intval($matches[2])
            ];
        }

        // Formato textual: Janeiro/2025, Jan/2025
        $meses = [
            'janeiro' => 1, 'jan' => 1,
            'fevereiro' => 2, 'fev' => 2,
            'março' => 3, 'mar' => 3,
            'abril' => 4, 'abr' => 4,
            'maio' => 5, 'mai' => 5,
            'junho' => 6, 'jun' => 6,
            'julho' => 7, 'jul' => 7,
            'agosto' => 8, 'ago' => 8,
            'setembro' => 9, 'set' => 9,
            'outubro' => 10, 'out' => 10,
            'novembro' => 11, 'nov' => 11,
            'dezembro' => 12, 'dez' => 12
        ];

        foreach ($meses as $nome => $numero) {
            if (preg_match('/' . $nome . '[\/\-\s]+(\d{4})/i', $stringPeriodo, $matches)) {
                return [
                    'mes' => $numero,
                    'ano' => intval($matches[1])
                ];
            }
        }

        return null;
    }

    // Processar PDF completo e extrair todos os dados
    public function processarPdf($caminhoArquivo, $textoExtraido = null)
    {
        if (!$textoExtraido) {
            // TODO: Implementar extração de texto do PDF
            // Por enquanto retorna null
            return null;
        }

        $dados = [
            'template_id' => $this->id,
            'template_nome' => $this->nome,
            'tipo_extracao' => $this->tipo_extracao
        ];

        // Extrair CNPJ
        $dados['cnpj'] = $this->extrairCnpj($textoExtraido);

        // Extrair período
        $periodo = $this->extrairPeriodo($textoExtraido);
        if ($periodo) {
            $dados['mes_competencia'] = $periodo['mes'];
            $dados['ano_competencia'] = $periodo['ano'];
        }

        // Extrair valor
        $dados['valor'] = $this->extrairValor($textoExtraido);

        // Campos customizados
        if (!empty($this->campos_extracao)) {
            foreach ($this->campos_extracao as $campo => $padrao) {
                if (preg_match('/' . $padrao . '/', $textoExtraido, $matches)) {
                    $dados[$campo] = $matches[0];
                }
            }
        }

        return $dados;
    }

    // Marcar como aprovado no teste
    public function aprovarTeste()
    {
        $this->update([
            'teste_aprovado' => true,
            'ultima_validacao' => now()
        ]);
    }

    // Marcar como reprovado no teste
    public function reprovarTeste()
    {
        $this->update([
            'teste_aprovado' => false,
            'ultima_validacao' => now()
        ]);
    }

    // Accessor para tipo de extração formatado
    public function getTipoExtracaoFormatadoAttribute()
    {
        $tipos = [
            self::TIPO_REGEX => 'Regex (Padrões)',
            self::TIPO_OCR => 'OCR (Coordenadas)',
            self::TIPO_POSICAO => 'Posição Fixa',
            self::TIPO_MISTO => 'Misto'
        ];

        return $tipos[$this->tipo_extracao] ?? 'Desconhecido';
    }

    // Accessor para status do teste
    public function getStatusTesteBadgeAttribute()
    {
        if ($this->teste_aprovado) {
            return '<span class="badge bg-success">✓ Aprovado</span>';
        } elseif ($this->ultima_validacao) {
            return '<span class="badge bg-danger">✗ Reprovado</span>';
        }
        return '<span class="badge bg-warning">⊘ Não testado</span>';
    }
}
