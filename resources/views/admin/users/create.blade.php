@extends('layouts.app')

@section('title', 'Novo Usuário - CloudeConta')

@section('content')
<div class="min-h-screen admin-gradient">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-user-plus text-white mr-3"></i>
                        Novo Usuário
                    </h1>
                    <p class="text-white text-opacity-75 mt-1">Criar um novo usuário no sistema</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-red-600 mr-2"></i>Nome Completo *
                        </label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('nome') border-red-500 @enderror"
                               placeholder="Nome completo do usuário">
                        @error('nome')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-red-600 mr-2"></i>Email *
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('email') border-red-500 @enderror"
                               placeholder="email@exemplo.com">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Senha -->
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-red-600 mr-2"></i>Senha *
                        </label>
                        <input type="password" id="senha" name="senha" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('senha') border-red-500 @enderror"
                               placeholder="Mínimo 6 caracteres">
                        @error('senha')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefone -->
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-red-600 mr-2"></i>Telefone
                        </label>
                        <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('telefone') border-red-500 @enderror"
                               placeholder="(11) 99999-9999">
                        @error('telefone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nível de Acesso -->
                    <div class="md:col-span-2">
                        <label for="nivel_acesso" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-shield-alt text-red-600 mr-2"></i>Nível de Acesso *
                        </label>
                        <select id="nivel_acesso" name="nivel_acesso" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 @error('nivel_acesso') border-red-500 @enderror">
                            <option value="">Selecione o nível de acesso</option>
                            <option value="usuario" {{ old('nivel_acesso') == 'usuario' ? 'selected' : '' }}>Usuário</option>
                            <option value="admin" {{ old('nivel_acesso') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('nivel_acesso')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="font-medium text-blue-900">👤 Usuário:</p>
                                <ul class="text-blue-700 text-xs mt-1 space-y-1">
                                    <li>• Acesso ao dashboard pessoal</li>
                                    <li>• Gerenciar suas empresas</li>
                                    <li>• Visualizar relatórios</li>
                                </ul>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="font-medium text-red-900">⚡ Administrador:</p>
                                <ul class="text-red-700 text-xs mt-1 space-y-1">
                                    <li>• Acesso completo ao sistema</li>
                                    <li>• Gerenciar todos os usuários</li>
                                    <li>• Configurações do sistema</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
