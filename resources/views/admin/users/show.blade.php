@extends('layouts.app')
@section('title', 'Detalhes do Usuário - CloudeConta')
@section('content')
<div class="min-h-screen admin-gradient">
    <div class="p-6">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-user text-white mr-3"></i>Detalhes do Usuário
                    </h1>
                    <p class="text-white text-opacity-75 mt-1">Informações completas de {{ $user->nome }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações Principais -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informações Pessoais
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nome Completo</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $user->nome }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telefone</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->telefone ?: 'Não informado' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Data de Cadastro</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Não disponível' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status e Permissões -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-shield-alt text-red-600 mr-2"></i>Permissões
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nível de Acesso</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 {{ $user->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                <i class="fas fa-{{ $user->isAdmin() ? 'crown' : 'user' }} mr-2"></i>
                                {{ $user->tipoUsuario }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 {{ $user->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ $user->isActive() ? 'check-circle' : 'times-circle' }} mr-2"></i>
                                {{ $user->isActive() ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>Ações Rápidas
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Editar Usuário
                        </a>
                        
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-{{ $user->isActive() ? 'ban' : 'check' }} mr-2"></i>
                                {{ $user->isActive() ? 'Desativar' : 'Ativar' }}
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" 
                              onsubmit="return confirm('Tem certeza que deseja deletar este usuário?')" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>Deletar Usuário
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection