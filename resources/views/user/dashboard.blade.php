@extends('layouts.app')

@section('title', 'Dashboard Usuário - CloudeConta')

@section('content')
<div class="min-h-screen gradient-bg">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-home text-white mr-3"></i>
                        Dashboard Usuário
                    </h1>
                    <p class="text-white text-opacity-75 mt-1">Bem-vindo, {{ $user->nome }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-white text-opacity-75">{{ $user->email }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Empresas -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Empresas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['empresas']['total'] }}</p>
                        <p class="text-sm text-blue-600">{{ $stats['empresas']['minhas'] }} minhas</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Lançamentos -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Lançamentos</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['lancamentos']['total'] }}</p>
                        <p class="text-sm text-green-600">{{ $stats['lancamentos']['este_mes'] }} este mês</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Fornecedores -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Fornecedores</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['fornecedores']['total'] }}</p>
                        <p class="text-sm text-purple-600">Cadastrados</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Área de Trabalho -->
            <a href="{{ route('workspace.index') }}" class="bg-white rounded-xl shadow-lg p-6 card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Área de Trabalho</p>
                        <p class="text-2xl font-bold text-gray-900">Workspace</p>
                        <p class="text-sm text-purple-600">Sistema Completo</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-desktop text-purple-600 text-xl"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Ações Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Menu Rápido -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Ações Rápidas
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <button class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg transition-colors">
                        <i class="fas fa-plus text-blue-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Nova Empresa</p>
                    </button>
                    <button class="bg-green-50 hover:bg-green-100 p-4 rounded-lg transition-colors">
                        <i class="fas fa-receipt text-green-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Lançamento</p>
                    </button>
                    <button class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg transition-colors">
                        <i class="fas fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Relatórios</p>
                    </button>
                    <button class="bg-orange-50 hover:bg-orange-100 p-4 rounded-lg transition-colors">
                        <i class="fas fa-cog text-orange-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-700">Configurações</p>
                    </button>
                </div>
            </div>

            <!-- Atividade Recente -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clock text-green-600 mr-2"></i>
                    Atividade Recente
                </h3>
                <div class="space-y-4">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="{{ $activity['icon'] }} text-gray-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
