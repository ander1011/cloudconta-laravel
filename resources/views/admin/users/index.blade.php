@extends('layouts.app')

@section('title', 'Gerenciar Usuários - CloudeConta')

@section('content')
<div class="min-h-screen admin-gradient">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-users text-white mr-3"></i>
                        Gerenciar Usuários
                    </h1>
                    <p class="text-white text-opacity-75 mt-1">Lista de todos os usuários do sistema</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Novo Usuário
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista Simples -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-list text-red-600 mr-2"></i>
                Lista de Usuários ({{ $users->count() }})
            </h3>
            
            <div class="space-y-4">
                @foreach($users as $user)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $user->nome }}</div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $user->tipoUsuario }}
                        </span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->isActive() ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2 border-l border-gray-200 pl-3">
                        <!-- Ver Detalhes -->
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50" title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <!-- Editar -->
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        @if($user->id !== auth()->id())
                        <!-- Ativar/Desativar -->
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-orange-600 hover:text-orange-800 p-1 rounded hover:bg-orange-50" title="{{ $user->isActive() ? 'Desativar' : 'Ativar' }}">
                                <i class="fas fa-{{ $user->isActive() ? 'ban' : 'check' }}"></i>
                            </button>
                        </form>
                        
                        <!-- Deletar -->
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline" 
                              onsubmit="return confirm('Tem certeza que deseja deletar {{ $user->nome }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50" title="Deletar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
