<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\ObrigacaoFiscalController;
use App\Http\Controllers\PeriodoFiscalController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\EmpresaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Autenticação Necessária)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // ===================================================================
    // SÓCIOS (QSA - Quadro Societário)
    // ===================================================================
    Route::prefix('socios')->group(function () {
        Route::get('/empresa/{empresaId}', [SocioController::class, 'index']); // Listar sócios da empresa
        Route::get('/{id}', [SocioController::class, 'show']); // Buscar sócio
        Route::post('/', [SocioController::class, 'store']); // Criar sócio
        Route::put('/{id}', [SocioController::class, 'update']); // Atualizar sócio
        Route::delete('/{id}', [SocioController::class, 'destroy']); // Deletar sócio
    });

    // ===================================================================
    // OBRIGAÇÕES FISCAIS (Vínculos Empresa ↔ Obrigação)
    // ===================================================================
    Route::prefix('obrigacoes-fiscais')->group(function () {
        Route::get('/empresa/{empresaId}', [ObrigacaoFiscalController::class, 'index']); // Listar obrigações da empresa
        Route::get('/empresa/{empresaId}/layouts-disponiveis', [ObrigacaoFiscalController::class, 'layoutsDisponiveis']); // Layouts para vincular
        Route::post('/', [ObrigacaoFiscalController::class, 'store']); // Vincular obrigação
        Route::put('/{id}', [ObrigacaoFiscalController::class, 'update']); // Atualizar vínculo
        Route::delete('/{id}', [ObrigacaoFiscalController::class, 'destroy']); // Remover vínculo
        Route::post('/{id}/gerar-periodos', [ObrigacaoFiscalController::class, 'gerarPeriodos']); // Gerar períodos fiscais
    });

    // ===================================================================
    // PERÍODOS FISCAIS (Vencimentos)
    // ===================================================================
    Route::prefix('periodos-fiscais')->group(function () {
        Route::get('/dashboard', [PeriodoFiscalController::class, 'dashboard']); // Dashboard: hoje, próximos, atrasados
        Route::get('/calendario', [PeriodoFiscalController::class, 'calendario']); // Calendário de vencimentos
        Route::get('/', [PeriodoFiscalController::class, 'index']); // Listar com filtros
        Route::get('/{id}', [PeriodoFiscalController::class, 'show']); // Buscar período
        Route::put('/{id}', [PeriodoFiscalController::class, 'update']); // Atualizar período
        Route::post('/{id}/marcar-pago', [PeriodoFiscalController::class, 'marcarPago']); // Marcar como pago
        Route::post('/{id}/marcar-dispensado', [PeriodoFiscalController::class, 'marcarDispensado']); // Sem movimento
    });

    // ===================================================================
    // DOCUMENT TEMPLATES (Extração Automática de PDF)
    // ===================================================================
    Route::prefix('document-templates')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index']); // Listar templates
        Route::get('/{id}', [DocumentTemplateController::class, 'show']); // Buscar template
        Route::post('/', [DocumentTemplateController::class, 'store']); // Criar template
        Route::put('/{id}', [DocumentTemplateController::class, 'update']); // Atualizar template
        Route::delete('/{id}', [DocumentTemplateController::class, 'destroy']); // Deletar template
        Route::post('/{id}/testar', [DocumentTemplateController::class, 'testar']); // Testar extração
        Route::post('/{id}/aprovar-teste', [DocumentTemplateController::class, 'aprovarTeste']); // Aprovar
        Route::post('/{id}/reprovar-teste', [DocumentTemplateController::class, 'reprovarTeste']); // Reprovar
    });

    // ===================================================================
    // EMPRESAS (Consultas e Atualizações)
    // ===================================================================
    Route::prefix('empresas')->group(function () {
        Route::get('/{empresa}/detalhes-completos', [EmpresaController::class, 'detalhesCompletos']); // Detalhes completos
        Route::post('/{empresa}/atualizar-brasilapi', [EmpresaController::class, 'atualizarDadosBrasilApi']); // Atualizar BrasilAPI
        Route::post('/{empresa}/configurar-pasta-backup', [EmpresaController::class, 'configurarPastaBackup']); // Configurar pasta backup
        Route::post('/consultar-cnpj', [EmpresaController::class, 'consultarCnpj']); // Consultar CNPJ
    });

});
