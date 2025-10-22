<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\Empresa;
use App\Models\EmpresaModulo;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    /**
     * Lista todos os módulos disponíveis
     */
    public function index()
    {
        $modulos = Modulo::ativos()->ordenados()->get();
        
        return view('admin.modulos.index', compact('modulos'));
    }

    /**
     * Mostra painel de gestão de módulos por empresa
     */
    public function gerenciarEmpresa($empresaId)
    {
        $empresa = Empresa::with('modulosAtivos')->findOrFail($empresaId);
        $todosModulos = Modulo::ativos()->ordenados()->get();
        
        // Criar array de módulos ativos da empresa
        $modulosAtivos = $empresa->modulosAtivos->pluck('id')->toArray();
        
        return view('admin.modulos.gerenciar-empresa', compact('empresa', 'todosModulos', 'modulosAtivos'));
    }

    /**
     * Ativar módulo para uma empresa
     */
    public function ativar(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'modulo_id' => 'required|exists:modulos,id',
            'data_expiracao' => 'nullable|date'
        ]);

        $empresa = Empresa::findOrFail($request->empresa_id);
        $modulo = Modulo::findOrFail($request->modulo_id);

        // Verificar se já existe
        $empresaModulo = EmpresaModulo::where('empresa_id', $empresa->id)
                                     ->where('modulo_id', $modulo->id)
                                     ->first();

        if ($empresaModulo) {
            // Reativar se já existe
            $empresaModulo->update([
                'ativo' => true,
                'data_ativacao' => now(),
                'data_expiracao' => $request->data_expiracao
            ]);
        } else {
            // Criar novo relacionamento
            EmpresaModulo::create([
                'empresa_id' => $empresa->id,
                'modulo_id' => $modulo->id,
                'ativo' => true,
                'data_ativacao' => now(),
                'data_expiracao' => $request->data_expiracao
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Módulo '{$modulo->nome}' ativado para {$empresa->nome}"
        ]);
    }

    /**
     * Desativar módulo para uma empresa
     */
    public function desativar(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'modulo_id' => 'required|exists:modulos,id'
        ]);

        $empresaModulo = EmpresaModulo::where('empresa_id', $request->empresa_id)
                                     ->where('modulo_id', $request->modulo_id)
                                     ->firstOrFail();

        $empresaModulo->desativar();

        $modulo = Modulo::find($request->modulo_id);
        $empresa = Empresa::find($request->empresa_id);

        return response()->json([
            'success' => true,
            'message' => "Módulo '{$modulo->nome}' desativado para {$empresa->nome}"
        ]);
    }

    /**
     * Lista de empresas com seus módulos
     */
    public function empresas()
    {
        $empresas = Empresa::with(['modulos' => function($query) {
            $query->wherePivot('ativo', true);
        }])->get();

        return view('admin.modulos.empresas', compact('empresas'));
    }
}
