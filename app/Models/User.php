<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome', 'email', 'senha', 'nivel_acesso', 'ativo', 'telefone'
    ];
    public $timestamps = false;
    protected $hidden = ['senha', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function isAdmin(): bool
    {
        return in_array($this->nivel_acesso, ['admin', 'administrador']);
    }

    public function isActive(): bool
    {
        return $this->ativo == 1;
    }

    public function getTipoUsuarioAttribute(): string
    {
        return $this->isAdmin() ? 'Administrador' : 'Usuário';
    }

    public function getDashboardRouteAttribute(): string
    {
        return $this->isAdmin() ? 'admin.dashboard' : 'user.dashboard';
    }
}
