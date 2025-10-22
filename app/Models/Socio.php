<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $table = 'socios';
    
    protected $fillable = [
        'empresa_id',
        'nome_socio',
        'qualificacao_socio',
        'data_entrada_sociedade',
        'faixa_etaria',
        'cpf_cnpj_socio',
        'percentual_capital',
        'pais'
    ];

    protected $casts = [
        'data_entrada_sociedade' => 'date',
        'percentual_capital' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Scopes
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Accessor para CPF/CNPJ formatado
    public function getCpfCnpjFormatadoAttribute()
    {
        if (!$this->cpf_cnpj_socio) return 'N/A';

        $doc = preg_replace('/\D/', '', $this->cpf_cnpj_socio);
        
        if (strlen($doc) == 11) {
            // CPF: 123.456.789-00
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        } elseif (strlen($doc) == 14) {
            // CNPJ: 12.345.678/0001-00
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }

        return $this->cpf_cnpj_socio;
    }

    // Accessor para percentual formatado
    public function getPercentualFormatadoAttribute()
    {
        return $this->percentual_capital ? number_format($this->percentual_capital, 2, ',', '') . '%' : 'N/A';
    }

    // Accessor para qualificação formatada
    public function getQualificacaoFormatadaAttribute()
    {
        $qualificacoes = [
            '05' => 'Administrador',
            '08' => 'Conselheiro de Administração',
            '10' => 'Diretor',
            '16' => 'Presidente',
            '17' => 'Procurador',
            '20' => 'Sociedade Consorciada',
            '21' => 'Sociedade Filiada',
            '22' => 'Sócio',
            '23' => 'Sócio Capitalista',
            '24' => 'Sócio Comanditado',
            '25' => 'Sócio Comanditário',
            '26' => 'Sócio de Indústria',
            '28' => 'Sócio-Gerente',
            '29' => 'Sócio Incapaz ou Relat.Incapaz (exceto menor)',
            '30' => 'Sócio Menor (Assistido/Representado)',
            '31' => 'Sócio Ostensivo',
            '37' => 'Sócio Pessoa Jurídica Domiciliado no Exterior',
            '38' => 'Sócio Pessoa Física Residente no Exterior',
            '47' => 'Sócio Pessoa Física Residente no Brasil',
            '48' => 'Sócio Pessoa Jurídica Domiciliado no Brasil',
            '49' => 'Sócio-Administrador',
            '50' => 'Empresário',
            '52' => 'Sócio com Capital',
            '53' => 'Sócio sem Capital',
            '54' => 'Fundador',
            '55' => 'Sócio Comanditado Residente no Exterior',
            '56' => 'Sócio Comanditário Pessoa Física Residente no Exterior',
            '57' => 'Sócio Comanditário Pessoa Jurídica Domiciliado no Exterior',
            '58' => 'Sócio Comanditário Incapaz',
            '59' => 'Produtor Rural',
            '63' => 'Cotas em Tesouraria',
            '65' => 'Titular Pessoa Física Residente ou Domiciliado no Brasil',
            '66' => 'Titular Pessoa Física Residente ou Domiciliado no Exterior',
            '67' => 'Titular Pessoa Física Incapaz ou Relativamente Incapaz',
            '68' => 'Titular Pessoa Jurídica Domiciliado no Brasil',
            '69' => 'Titular Pessoa Jurídica Domiciliado no Exterior',
            '70' => 'Administrador Judicial',
            '71' => 'Liquidante',
            '72' => 'Interventor',
            '74' => 'Síndico ou Comissário',
            '75' => 'Representante da Empresa',
            '76' => 'Ministro de Estado das Relações Exteriores',
            '77' => 'Responsável',
            '78' => 'Assistente',
            '79' => 'Tutor',
            '80' => 'Curador',
            '81' => 'Inventariante'
        ];

        return $qualificacoes[$this->qualificacao_socio] ?? $this->qualificacao_socio;
    }
}
