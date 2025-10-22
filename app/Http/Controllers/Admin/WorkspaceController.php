<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkspaceController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            
            // Buscar empresas do usuário
            $empresas = Empresa::where('usuario_id', $user->id)
                              ->where('ativo', true)
                              ->orderBy('nome')
                              ->get();
            
            // Empresa selecionada (session ou primeira)
            $empresaSelecionadaId = session('empresa_selecionada_id');
            
            if ($empresaSelecionadaId) {
                $empresaSelecionada = $empresas->firstWhere('id', $empresaSelecionadaId);
            } else {
                $empresaSelecionada = $empresas->first();
                if ($empresaSelecionada) {
                    session(['empresa_selecionada_id' => $empresaSelecionada->id]);
                }
            }
            
            // Estatísticas baseadas no nível do usuário
            $stats = [
                'users_total' => $user->isAdmin() ? User::count() : 0,
                'users_active' => $user->isAdmin() ? User::where('ativo', 1)->count() : 0,
                'users_admin' => $user->isAdmin() ? User::where('nivel_acesso', 'admin')->count() : 0,
                'empresas_total' => $empresas->count(),
                'empresas_active' => $empresas->count(),
                'can_manage_users' => $user->isAdmin(),
                'can_manage_system' => $user->isAdmin(),
            ];
            
            return view('admin.workspace.index', compact('user', 'stats', 'empresas', 'empresaSelecionada'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao carregar workspace: ' . $e->getMessage());
        }
    }
    
    public function selectEmpresa(Request $request)
    {
        $empresaId = $request->empresa_id;
        $user = auth()->user();
        
        // Validar se o usuário tem acesso à empresa
        $empresa = Empresa::where('id', $empresaId)
                         ->where('usuario_id', $user->id)
                         ->where('ativo', true)
                         ->first();
        
        if (!$empresa) {
            return response()->json([
                'success' => false, 
                'message' => 'Você não tem acesso a esta empresa'
            ], 403);
        }
        
        // Salvar na sessão
        session(['empresa_selecionada_id' => $empresaId]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Empresa selecionada: ' . $empresa->nome,
            'empresa' => [
                'id' => $empresa->id,
                'nome' => $empresa->nome,
                'cnpj' => $empresa->cnpj_formatado
            ]
        ]);
    }
}
