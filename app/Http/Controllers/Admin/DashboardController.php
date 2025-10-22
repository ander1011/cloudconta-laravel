<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'users' => [
                    'total' => User::count(),
                    'active' => User::where('ativo', 1)->count(),
                    'admins' => User::whereIn('nivel_acesso', ['admin', 'administrador'])->count()
                ],
                'empresas' => [
                    'total' => 0,
                    'active' => 0
                ],
                'system' => [
                    'uptime' => '5 dias',
                    'database_size' => '15 MB'
                ]
            ];

            $recentUsers = User::orderBy('id', 'desc')->limit(5)->get();
            
            return view('admin.dashboard', compact('stats', 'recentUsers'));
        } catch (\Exception $e) {
            return 'Erro: ' . $e->getMessage();
        }
    }
}
