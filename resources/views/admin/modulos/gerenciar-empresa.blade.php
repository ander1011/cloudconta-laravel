@extends('layouts.app')

@section('title', 'Gerenciar Módulos - ' . $empresa->nome)

@section('content')
<div class="min-h-screen admin-gradient py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">⚙️ Gerenciar Módulos</h1>
                    <p class="text-gray-600 mt-2">
                        <strong>{{ $empresa->nome }}</strong> - {{ $empresa->cnpj_formatado }}
                    </p>
                </div>
                <a href="{{ route('admin.modulos.empresas') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>

        <!-- Grid de Módulos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($todosModulos as $modulo)
            @php
                $ativo = in_array($modulo->id, $modulosAtivos);
            @endphp
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-all hover:scale-105 {{ $ativo ? 'ring-4 ring-green-400' : '' }}">
                <!-- Cabeçalho do Card -->
                <div class="p-6 {{ $ativo ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-gray-400 to-gray-500' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center text-3xl">
                            <i class="{{ $modulo->icone }} text-white"></i>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($ativo)
                            <span class="px-2 py-1 bg-white text-green-600 text-xs font-bold rounded">ATIVO</span>
                            @else
                            <span class="px-2 py-1 bg-white text-gray-600 text-xs font-bold rounded">INATIVO</span>
                            @endif
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-white">{{ $modulo->nome }}</h3>
                </div>

                <!-- Corpo do Card -->
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4">{{ $modulo->descricao }}</p>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Preço Mensal:</span>
                            <span class="text-lg font-bold text-gray-900">{{ $modulo->preco_formatado }}</span>
                        </div>
                    </div>

                    <!-- Botão Toggle -->
                    <button onclick="toggleModulo({{ $modulo->id }}, {{ $ativo ? 'true' : 'false' }})"
                            id="btn-modulo-{{ $modulo->id }}"
                            class="w-full py-3 rounded-lg font-semibold transition-all transform hover:scale-105 
                                {{ $ativo ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                        <i class="fas {{ $ativo ? 'fa-times-circle' : 'fa-check-circle' }} mr-2"></i>
                        <span id="text-modulo-{{ $modulo->id }}">
                            {{ $ativo ? 'Desativar Módulo' : 'Ativar Módulo' }}
                        </span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Resumo -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">📊 Resumo de Módulos</h3>
                    <p class="text-gray-600">
                        <strong class="text-green-600">{{ count($modulosAtivos) }}</strong> módulos ativos de 
                        <strong>{{ $todosModulos->count() }}</strong> disponíveis
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 mb-1">Valor Total Mensal:</p>
                    <p class="text-3xl font-bold text-gray-900" id="valor-total">
                        R$ {{ number_format($todosModulos->whereIn('id', $modulosAtivos)->sum('preco_mensal'), 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast de Notificação -->
<div id="toast" class="fixed top-4 right-4 bg-white rounded-lg shadow-2xl p-4 transform translate-x-full transition-transform duration-300 z-50" style="min-width: 300px;">
    <div class="flex items-center">
        <div id="toast-icon" class="flex-shrink-0"></div>
        <div class="ml-3 flex-1">
            <p id="toast-message" class="text-sm font-medium text-gray-900"></p>
        </div>
        <button onclick="hideToast()" class="ml-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
const empresaId = {{ $empresa->id }};
let modulosAtivos = @json($modulosAtivos);

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toast-icon');
    const messageEl = document.getElementById('toast-message');
    
    // Definir ícone e cor
    if (type === 'success') {
        icon.innerHTML = '<div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center"><i class="fas fa-check text-white"></i></div>';
    } else {
        icon.innerHTML = '<div class="h-10 w-10 bg-red-500 rounded-full flex items-center justify-center"><i class="fas fa-exclamation text-white"></i></div>';
    }
    
    messageEl.textContent = message;
    
    // Mostrar toast
    toast.classList.remove('translate-x-full');
    
    // Esconder após 4 segundos
    setTimeout(() => {
        hideToast();
    }, 4000);
}

function hideToast() {
    const toast = document.getElementById('toast');
    toast.classList.add('translate-x-full');
}

async function toggleModulo(moduloId, estaAtivo) {
    const btn = document.getElementById(`btn-modulo-${moduloId}`);
    const text = document.getElementById(`text-modulo-${moduloId}`);
    const originalHTML = btn.innerHTML;
    
    // Loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processando...';
    
    try {
        const url = estaAtivo ? '{{ route("admin.modulos.desativar") }}' : '{{ route("admin.modulos.ativar") }}';
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                empresa_id: empresaId,
                modulo_id: moduloId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message, 'success');
            
            // Atualizar UI
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Erro ao processar', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    } catch (error) {
        console.error('Erro:', error);
        showToast('Erro ao processar requisição', 'error');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
