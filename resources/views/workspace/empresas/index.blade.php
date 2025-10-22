@extends('layouts.app')

@section('title', 'Gestão de Empresas')

@section('content')
<div class="min-h-screen gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏢 Gestão de Empresas</h1>
                    <p class="text-gray-600 mt-2">Gerencie suas empresas cadastradas</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('workspace.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-home mr-2"></i>Workspace
                    </a>
                    <a href="{{ route('workspace.empresas.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nova Empresa
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
            </div>
        @endif

        <!-- Lista de Empresas -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">
                    📋 Suas Empresas ({{ $empresas->count() }})
                </h2>
            </div>

            @if($empresas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Situação</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($empresas as $empresa)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $empresa->nome }}</div>
                                        @if($empresa->razao_social)
                                            <div class="text-sm text-gray-500">{{ $empresa->razao_social }}</div>
                                        @endif
                                        @if($empresa->nome_fantasia)
                                            <div class="text-sm text-blue-500">{{ $empresa->nome_fantasia }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $empresa->cnpj_formatado }}</code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($empresa->situacao_cadastral)
                                        @php
                                            $status = $empresa->situacao_status;
                                            $class = $status === 'active' ? 'bg-green-100 text-green-800' : ($status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class }}">
                                            {{ $empresa->situacao_cadastral }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Não consultado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $empresa->telefone_formatado ?: '—' }}</div>
                                    <div class="text-gray-500">{{ $empresa->email ?: '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($empresa->ativo)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('workspace.empresas.show', $empresa) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('workspace.empresas.edit', $empresa) }}" 
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmarExclusao({{ $empresa->id }})" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Nenhuma empresa cadastrada</h3>
                    <p class="text-gray-500 mb-6">Comece cadastrando sua primeira empresa</p>
                    <a href="{{ route('workspace.empresas.create') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Cadastrar Primeira Empresa
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="modalExclusao" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Exclusão</h3>
        <p class="text-gray-500 mb-6">Tem certeza que deseja excluir esta empresa? Esta ação não pode ser desfeita!</p>
        <div class="flex justify-end space-x-3">
            <button onclick="fecharModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition-colors">
                Cancelar
            </button>
            <form id="formExclusao" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors">
                    Sim, Excluir
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(empresaId) {
    document.getElementById('formExclusao').action = `/workspace/empresas/${empresaId}`;
    document.getElementById('modalExclusao').classList.remove('hidden');
    document.getElementById('modalExclusao').classList.add('flex');
}

function fecharModal() {
    document.getElementById('modalExclusao').classList.add('hidden');
    document.getElementById('modalExclusao').classList.remove('flex');
}
</script>
@endsection