@extends('layouts.app')
@section('title', 'Editar Usuário - CloudeConta')
@section('content')
<div class="min-h-screen admin-gradient">
    <div class="p-6">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-user-edit text-white mr-3"></i>Editar Usuário
                    </h1>
                    <p class="text-white text-opacity-75 mt-1">Modificar dados do usuário: {{ $user->nome }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-red-600 mr-2"></i>Nome Completo
                        </label>
                        <input type="text" name="nome" value="{{ $user->nome }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-red-600 mr-2"></i>Email
                        </label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-red-600 mr-2"></i>Nova Senha (deixe em branco para manter)
                        </label>
                        <input type="password" name="senha"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                               placeholder="Digite nova senha ou deixe em branco">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-red-600 mr-2"></i>Telefone
                        </label>
                        <input type="text" name="telefone" value="{{ $user->telefone }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-shield-alt text-red-600 mr-2"></i>Nível de Acesso
                        </label>
                        <select name="nivel_acesso" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="usuario" {{ $user->nivel_acesso == 'usuario' ? 'selected' : '' }}>Usuário</option>
                            <option value="admin" {{ $user->nivel_acesso == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection