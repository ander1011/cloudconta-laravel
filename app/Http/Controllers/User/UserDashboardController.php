<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            
            $stats = [
                'empresas' => ['total' => 5, 'minhas' => 2],
                'lancamentos' => ['total' => 10, 'este_mes' => 3],
                'fornecedores' => ['total' => 8]
            ];

            $recentActivity = [
                ['action' => 'Login realizado', 'time' => 'Agora', 'icon' => 'fas fa-sign-in-alt']
            ];
            
            return view('user.dashboard', compact('stats', 'recentActivity', 'user'));
        } catch (\Exception $e) {
            return 'Erro User Dashboard: ' . $e->getMessage();
        }
    }
}
