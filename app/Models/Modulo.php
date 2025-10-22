<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';
    
    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'icone',
        'preco_mensal',
        'ordem',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'preco_mensal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com empresas que possuem este módulo
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_modulos', 'modulo_id', 'empresa_id')
                    ->withPivot('ativo', 'data_ativacao', 'data_expiracao')
                    ->withTimestamps();
    }

    // Relacionamento com pagamentos
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'modulo_id');
    }

    // Scope para módulos ativos
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para ordenar por ordem
    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem', 'asc');
    }

    // Accessor para preço formatado
    public function getPrecoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_mensal, 2, ',', '.');
    }

    // Verifica se o módulo está ativo para uma empresa
    public function estaAtivoParaEmpresa($empresaId)
    {
        return $this->empresas()
                    ->where('empresa_id', $empresaId)
                    ->wherePivot('ativo', true)
                    ->exists();
    }
}
