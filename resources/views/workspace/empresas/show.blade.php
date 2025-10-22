@extends('layouts.app')

@section('title', 'Detalhes da Empresa')

@section('content')
<div class="min-h-screen gradient-bg py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏢 {{ $empresa->nome }}</h1>
                    <p class="text-gray-600 mt-2">Detalhes completos da empresa</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('workspace.empresas.edit', $empresa) }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    <a href="{{ route('workspace.empresas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informações Principais -->
            <div class="lg:col-span-2">
                <!-- Informações Básicas -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b">📋 Informações Básicas</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nome da Empresa</label>
                                <p class="text-gray-900 font-medium">{{ $empresa->nome }}</p>
                            </div>
                            
                            @if($empresa->razao_social)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Razão Social</label>
                                <p class="text-gray-900">{{ $empresa->razao_social }}</p>
                            </div>
                            @endif
                            
                            @if($empresa->nome_fantasia)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nome Fantasia</label>
                                <p class="text-blue-600">{{ $empresa->nome_fantasia }}</p>
                            </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">CNPJ</label>
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $empresa->cnpj_formatado }}</code>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            @if($empresa->situacao_cadastral)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Situação na Receita</label>
                                @php
                                    $status = $empresa->situacao_status;
                                    $class = $status === 'active' ? 'bg-green-100 text-green-800' : ($status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $class }}">
                                    {{ $empresa->situacao_cadastral }}
                                </span>
                            </div>
                            @endif
                            
                            @if($empresa->porte)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Porte</label>
                                <p class="text-gray-900">{{ $empresa->porte }}</p>
                            </div>
                            @endif
                            
                            @if($empresa->capital_social)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Capital Social</label>
                                <p class="text-gray-900 font-medium">{{ $empresa->capital_social_formatado }}</p>
                            </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                @if($empresa->ativo)
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Ativo</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contato -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b">📞 Contato</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">E-mail</label>
                            @if($empresa->email)
                                <a href="mailto:{{ $empresa->email }}" class="text-blue-600 hover:text-blue-800">{{ $empresa->email }}</a>
                            @else
                                <span class="text-gray-400">Não informado</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Telefone</label>
                            @if($empresa->telefone)
                                <a href="tel:{{ preg_replace('/\D/', '', $empresa->telefone) }}" class="text-blue-600 hover:text-blue-800">{{ $empresa->telefone_formatado }}</a>
                            @else
                                <span class="text-gray-400">Não informado</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($empresa->endereco)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Endereço</label>
                        <p class="text-gray-900">{{ $empresa->endereco }}</p>
                    </div>
                    @endif
                </div>

                <!-- Atividade Econômica -->
                @if($empresa->cnae_principal || $empresa->natureza_juridica)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 border-b">🏭 Atividade Econômica</h2>
                    
                    @if($empresa->cnae_principal)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">CNAE Principal</label>
                        <div class="flex items-center">
                            <code class="bg-gray-100 px-2 py-1 rounded text-sm mr-3">{{ $empresa->cnae_principal }}</code>
                            @if($empresa->cnae_descricao)
                                <span class="text-gray-900">{{ $empresa->cnae_descricao }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($empresa->natureza_juridica)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Natureza Jurídica</label>
                        <p class="text-gray-900">{{ $empresa->natureza_juridica }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Informações do Sistema -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">ℹ️ Informações do Sistema</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cadastrado em</label>
                            <p class="text-sm text-gray-900">{{ $empresa->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Última atualização</label>
                            <p class="text-sm text-gray-900">{{ $empresa->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Proprietário</label>
                            <p class="text-sm text-gray-900">{{ $empresa->usuario->nome ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Ações Disponíveis</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('workspace.empresas.edit', $empresa) }}" 
                           class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Editar Empresa
                        </a>
                        <button onclick="atualizarDadosReceita()" 
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-sync mr-2"></i>Atualizar da Receita
                        </button>
                        <button onclick="confirmarExclusao()" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>Excluir Empresa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="modalExclusao" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Exclusão</h3>
        <p class="text-gray-500 mb-6">Tem certeza que deseja excluir a empresa <strong>{{ $empresa->nome }}</strong>?</p>
        <p class="text-red-600 text-sm mb-6">Esta ação não pode ser desfeita!</p>
        <div class="flex justify-end space-x-3">
            <button onclick="fecharModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition-colors">
                Cancelar
            </button>
            <form method="POST" action="{{ route('workspace.empresas.destroy', $empresa) }}" class="inline">
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
function confirmarExclusao() {
    document.getElementById('modalExclusao').classList.remove('hidden');
    document.getElementById('modalExclusao').classList.add('flex');
}

function fecharModal() {
    document.getElementById('modalExclusao').classList.add('hidden');
    document.getElementById('modalExclusao').classList.remove('flex');
}

async function atualizarDadosReceita() {
    if (!confirm('Deseja atualizar os dados desta empresa consultando novamente a Receita Federal?')) {
        return;
    }
    
    alert('Funcionalidade em desenvolvimento');
}
</script>
@endsection