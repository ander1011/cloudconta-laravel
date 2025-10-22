<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Empresa;

class VerificarAcessoModulo
{
    /**
     * Verifica se a empresa selecionada tem acesso ao módulo
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduloSlug
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $moduloSlug)
    {
        // Pegar empresa selecionada da sessão
        $empresaId = session('empresa_selecionada_id');
        
        if (!$empresaId) {
            return redirect()->route('workspace.index')
                           ->with('error', 'Nenhuma empresa selecionada. Por favor, selecione uma empresa.');
        }

        // Buscar empresa
        $empresa = Empresa::find($empresaId);
        
        if (!$empresa) {
            return redirect()->route('workspace.index')
                           ->with('error', 'Empresa não encontrada.');
        }

        // Verificar se tem acesso ao módulo
        if (!$empresa->temAcessoAoModulo($moduloSlug)) {
            return redirect()->route('workspace.index')
                           ->with('warning', 'Você não tem acesso ao módulo solicitado. Entre em contato com o administrador para ativar este módulo.');
        }

        // Compartilhar empresa com as views
        view()->share('empresaAtual', $empresa);

        return $next($request);
    }
}
