<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaModulo extends Model
{
    protected $table = 'empresa_modulos';
    
    protected $fillable = [
        'empresa_id',
        'modulo_id',
        'ativo',
        'data_ativacao',
        'data_expiracao'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_ativacao' => 'datetime',
        'data_expiracao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com módulo
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    // Scope para módulos ativos
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para módulos de uma empresa específica
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Verifica se o módulo ainda está válido (não expirou)
    public function estaValido()
    {
        if (!$this->ativo) {
            return false;
        }

        if ($this->data_expiracao && $this->data_expiracao->isPast()) {
            return false;
        }

        return true;
    }

    // Ativar módulo
    public function ativar()
    {
        $this->update([
            'ativo' => true,
            'data_ativacao' => now()
        ]);
    }

    // Desativar módulo
    public function desativar()
    {
        $this->update([
            'ativo' => false
        ]);
    }
}
