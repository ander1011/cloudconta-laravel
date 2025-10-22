@extends('layouts.app')

@section('title', 'Dashboard Administrativo - CloudeConta')

@section('content')
<div class="min-h-screen admin-gradient">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-tachometer-alt text-white mr-3"></i>
                        Dashboard Administrativo
                    </h1>
                    <p class="text-white text-opacity-75 mt-1">Visão geral do sistema CloudeConta</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-white text-opacity-75">{{ auth()->user()->nome }}</span>
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
            <!-- Usuários -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total de Usuários</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['users']['total'] }}</p>
                        <p class="text-sm text-green-600">{{ $stats['users']['active'] }} ativos</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Empresas -->
            <a href="{{ route('admin.modulos.empresas') }}" class="bg-white rounded-xl shadow-lg p-6 card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Empresas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['empresas']['total'] }}</p>
                        <p class="text-sm text-green-600">{{ $stats['empresas']['active'] }} ativas</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-building text-green-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <!-- Sistema -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sistema</p>
                        <p class="text-2xl font-bold text-gray-900">Online</p>
                        <p class="text-sm text-gray-600">Uptime: {{ $stats['system']['uptime'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-server text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu de Ações -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Gerenciar Usuários -->
            <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow-lg p-6 card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Gerenciar</p>
                        <p class="text-2xl font-bold text-gray-900">Usuários</p>
                        <p class="text-sm text-blue-600">Criar, editar, excluir</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-users-cog text-blue-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <!-- Configurações -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Configurações</p>
                        <p class="text-2xl font-bold text-gray-900">Sistema</p>
                        <p class="text-sm text-purple-600">Em breve</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cogs text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Relatórios -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Relatórios</p>
                        <p class="text-2xl font-bold text-gray-900">Avançados</p>
                        <p class="text-sm text-green-600">Em breve</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Workspace -->
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

            <!-- Gerenciar Módulos -->
            <a href="{{ route('admin.modulos.empresas') }}" class="bg-white rounded-xl shadow-lg p-6 card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Gerenciar</p>
                        <p class="text-2xl font-bold text-gray-900">Módulos</p>
                        <p class="text-sm text-orange-600">Ativar/Desativar</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-th-large text-orange-600 text-xl"></i>
                    </div>
                </div>
            </a>
        </div> 

        <!-- Usuários Recentes -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    Usuários Recentes
                </h3>
            </div>
            <div class="p-6">
                @forelse($recentUsers as $user)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $user->nome }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $user->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $user->tipoUsuario }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-3xl mb-2"></i>
                    <p class="text-gray-500">Nenhum usuário encontrado</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
