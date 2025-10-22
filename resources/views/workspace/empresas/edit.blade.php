@extends('layouts.app')

@section('title', 'Editar Empresa')

@section('content')
<div class="min-h-screen gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏢 Editar Empresa</h1>
                    <p class="text-gray-600 mt-2">{{ $empresa->nome }} - {{ $empresa->cnpj_formatado }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('workspace.empresas.show', $empresa) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-eye mr-2"></i>Visualizar
                    </a>
                    <a href="{{ route('workspace.empresas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
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

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <p class="font-bold mb-2">Erro ao salvar:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário de Edição -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">📋 Editar Dados</h2>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('workspace.empresas.update', $empresa) }}">
                            @csrf
                            @method('PUT')
                            
                            <!-- Nome -->
                            <div class="mb-6">
                                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome da Empresa <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nome') border-red-500 @enderror" 
                                       id="nome" name="nome" 
                                       value="{{ old('nome', $empresa->nome) }}" 
                                       placeholder="Nome ou Razão Social" required>
                                @error('nome')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    E-mail
                                </label>
                                <input type="email" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $empresa->email) }}" 
                                       placeholder="contato@empresa.com.br">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telefone -->
                            <div class="mb-6">
                                <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefone
                                </label>
                                <input type="text" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefone') border-red-500 @enderror" 
                                       id="telefone" name="telefone" 
                                       value="{{ old('telefone', $empresa->telefone) }}" 
                                       placeholder="(11) 99999-9999">
                                @error('telefone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Endereço -->
                            <div class="mb-6">
                                <label for="endereco" class="block text-sm font-medium text-gray-700 mb-2">
                                    Endereço
                                </label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('endereco') border-red-500 @enderror" 
                                          id="endereco" name="endereco" rows="3" 
                                          placeholder="Endereço completo">{{ old('endereco', $empresa->endereco) }}</textarea>
                                @error('endereco')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Botões -->                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-save mr-2"></i>Salvar Alterações
                                </button>
                                <a href="{{ route('workspace.empresas.show', $empresa) }}" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar com Dados da Receita Federal -->
            <div class="lg:col-span-1">
                <!-- Dados da Receita Federal -->
                <div class="bg-white rounded-lg shadow-lg mb-6 border-l-4 border-green-500">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">🏛️ Dados da Receita Federal</h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Estes dados não podem ser editados pois vêm da Receita Federal
                            </p>
                        </div>
                        
                        @if($empresa->razao_social)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Razão Social:</label>
                            <p class="text-gray-900">{{ $empresa->razao_social }}</p>
                        </div>
                        @endif
                        
                        @if($empresa->nome_fantasia)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Fantasia:</label>
                            <p class="text-gray-900">{{ $empresa->nome_fantasia }}</p>
                        </div>
                        @endif
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">CNPJ:</label>
                            <p class="font-mono text-gray-900 bg-gray-100 px-3 py-2 rounded">{{ $empresa->cnpj_formatado }}</p>
                        </div>
                        
                        @if($empresa->situacao_cadastral)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Situação:</label>
                            <div>
                                @php
                                    $status = $empresa->situacao_status;
                                    $colors = [
                                        'active' => 'bg-green-100 text-green-800 border-green-300',
                                        'suspended' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'inactive' => 'bg-red-100 text-red-800 border-red-300',
                                        'unknown' => 'bg-gray-100 text-gray-800 border-gray-300'
                                    ];
                                    $colorClass = $colors[$status] ?? $colors['unknown'];
                                @endphp
                                <span class="inline-block px-3 py-1 border rounded-full text-sm {{ $colorClass }}">
                                    {{ $empresa->situacao_cadastral }}
                                </span>
                            </div>
                        </div>
                        @endif
                        
                        @if($empresa->capital_social)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Capital Social:</label>
                            <p class="text-gray-900">{{ $empresa->capital_social_formatado }}</p>
                        </div>
                        @endif
                        
                        @if($empresa->cnae_principal)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">CNAE Principal:</label>
                            <p class="font-mono text-sm text-gray-900 bg-gray-100 px-3 py-2 rounded">{{ $empresa->cnae_principal }}</p>
                            @if($empresa->cnae_descricao)
                                <p class="text-xs text-gray-600 mt-1">{{ $empresa->cnae_descricao }}</p>
                            @endif
                        </div>
                        @endif
                        
                        <hr class="my-4">
                        <button type="button" 
                                onclick="atualizarDadosReceita()" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-sync mr-2"></i>Atualizar da Receita
                        </button>
                    </div>
                </div>

                <!-- Informações do Sistema -->
                <div class="bg-white rounded-lg shadow-lg border-l-4 border-blue-500">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">ℹ️ Informações do Sistema</h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cadastrado em:</label>
                            <p class="text-gray-900">{{ $empresa->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Última atualização:</label>
                            <p class="text-gray-900">{{ $empresa->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status:</label>
                            <div>
                                @if($empresa->ativo)
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 border border-green-300 rounded-full text-sm">
                                        <i class="fas fa-check-circle mr-1"></i>Ativo
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 border border-red-300 rounded-full text-sm">
                                        <i class="fas fa-times-circle mr-1"></i>Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Formatação automática do telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
    }
    
    e.target.value = value;
});

async function atualizarDadosReceita() {
    if (!confirm('Deseja atualizar os dados desta empresa consultando novamente a Receita Federal?')) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Atualizando...';
    
    try {
        const response = await fetch(`{{ route('workspace.empresas.consultar-cnpj') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ cnpj: '{{ $empresa->cnpj }}' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Dados atualizados com sucesso! A página será recarregada.');
            location.reload();
        } else {
            alert('Erro ao atualizar dados: ' + data.message);
            button.disabled = false;
            button.innerHTML = originalText;
        }
    } catch (error) {
        alert('Erro ao consultar CNPJ: ' + error.message);
        button.disabled = false;
        button.innerHTML = originalText;
    }
}
</script>
@endsection
