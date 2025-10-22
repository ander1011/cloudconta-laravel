<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WorkspaceController;
use App\Http\Controllers\Admin\ModuloController;

// Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('workspace.index');
        }
    }
    return view('landing');
})->name('home');

// Autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Adicionar após a linha 29 (depois das rotas de register)
Route::get('/ping', function() {
    return response()->json(['status' => 'ok', 'time' => now()]);
})->middleware('auth');

// Dashboard User
Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
});

// WORKSPACE - PARA TODOS
Route::middleware(['auth'])->group(function () {
    Route::get('/workspace', [WorkspaceController::class, 'index'])->name('workspace.index');
    Route::post('/workspace/select-empresa', [WorkspaceController::class, 'selectEmpresa'])->name('workspace.select-empresa');
    
    // Gestão de Empresas
    Route::prefix('workspace')->name('workspace.')->group(function () {
        Route::resource('empresas', EmpresaController::class);
        Route::post('empresas/consultar-cnpj', [EmpresaController::class, 'consultarCnpj'])->name('empresas.consultar-cnpj');
    });
});

// Dashboard Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Gestão de Módulos
    Route::prefix('modulos')->name('modulos.')->group(function () {
        Route::get('/', [ModuloController::class, 'index'])->name('index');
        Route::get('/empresas', [ModuloController::class, 'empresas'])->name('empresas');
        Route::get('/gerenciar-empresa/{empresa}', [ModuloController::class, 'gerenciarEmpresa'])->name('gerenciar-empresa');
        Route::post('/ativar', [ModuloController::class, 'ativar'])->name('ativar');
        Route::post('/desativar', [ModuloController::class, 'desativar'])->name('desativar');
    });
});