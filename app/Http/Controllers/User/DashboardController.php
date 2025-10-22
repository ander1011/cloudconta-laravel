<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            
            $stats = [
                'empresas' => [
                    'total' => DB::table('empresas')->count(),
                    'minhas' => 0 // Implementar depois o relacionamento
                ],
                'lancamentos' => [
                    'total' => DB::table('lancamentos')->count(),
                    'este_mes' => DB::table('lancamentos')->whereMonth('created_at', now()->month)->count()
                ],
                'fornecedores' => [
                    'total' => DB::table('fornecedores')->count()
                ]
            ];

            $recentActivity = [
                ['action' => 'Login realizado', 'time' => 'Agora', 'icon' => 'fas fa-sign-in-alt'],
                ['action' => 'Dashboard acessado', 'time' => 'Há 2 min', 'icon' => 'fas fa-tachometer-alt']
            ];
            
            return view('user.dashboard', compact('stats', 'recentActivity', 'user'));
        } catch (\Exception $e) {
            return 'Erro User Dashboard: ' . $e->getMessage();
        }
    }
}
