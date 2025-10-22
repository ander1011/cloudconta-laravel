@extends('layouts.app')

@section('title', 'Nova Empresa')

@section('content')
<div class="min-h-screen gradient-bg py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏢 Nova Empresa</h1>
                    <p class="text-gray-600 mt-2">Cadastre uma nova empresa com consulta automática na Receita Federal</p>
                </div>
                <a href="{{ route('workspace.empresas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">📋 Dados da Empresa</h2>
                    
                    <form method="POST" action="{{ route('workspace.empresas.store') }}">
                        @csrf
                        
                        <!-- CNPJ -->
                        <div class="mb-6">
                            <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-2">
                                CNPJ <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <input type="text" id="cnpj" name="cnpj" required maxlength="18"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="00.000.000/0000-00">
                                <button type="button" id="btnConsultarCNPJ"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-r-lg transition-colors">
                                    🔍 Consultar
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Digite o CNPJ para consulta automática na Receita Federal</p>
                        </div>

                        <!-- Loading -->
                        <div id="consultaLoading" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-3"></div>
                                <span class="text-blue-700">Consultando CNPJ na Receita Federal...</span>
                            </div>
                        </div>

                        <!-- Dados Consultados -->
                        <div id="dadosConsultados" class="hidden bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <h3 class="text-green-800 font-semibold mb-3">✅ Dados encontrados na Receita Federal:</h3>
                            <div id="dadosReceita" class="text-sm text-green-700"></div>
                        </div>

                        <!-- Nome -->
                        <div class="mb-4">
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome da Empresa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nome" name="nome" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nome ou Razão Social">
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                            <input type="email" id="email" name="email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="contato@empresa.com.br">
                        </div>

                        <!-- Telefone -->
                        <div class="mb-4">
                            <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                            <input type="text" id="telefone" name="telefone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="(11) 99999-9999">
                        </div>

                        <!-- Endereço -->
                        <div class="mb-6">
                            <label for="endereco" class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                            <textarea id="endereco" name="endereco" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Endereço completo"></textarea>
                        </div>

                        <!-- Botões -->
                        <div class="flex space-x-4">
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Cadastrar Empresa
                            </button>
                            <a href="{{ route('workspace.empresas.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dicas -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">💡 Dicas</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Digite apenas números do CNPJ ou use a formatação completa
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Os dados serão preenchidos automaticamente após consulta na Receita
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Você pode editar qualquer informação antes de salvar
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2">•</span>
                            Email e telefone podem ser atualizados depois
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Formatação CNPJ
document.getElementById('cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 14) {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }
    e.target.value = value;
});

// Consulta CNPJ
document.getElementById('btnConsultarCNPJ').addEventListener('click', function() {
    const cnpj = document.getElementById('cnpj').value.replace(/\D/g, '');
    
    if (cnpj.length !== 14) {
        alert('Digite um CNPJ válido com 14 dígitos');
        return;
    }
    
    consultarCNPJ(cnpj);
});

async function consultarCNPJ(cnpj) {
    const loading = document.getElementById('consultaLoading');
    const dadosConsultados = document.getElementById('dadosConsultados');
    const btnConsultar = document.getElementById('btnConsultarCNPJ');
    
    // Mostrar loading
    loading.classList.remove('hidden');
    dadosConsultados.classList.add('hidden');
    btnConsultar.disabled = true;
    
    try {
        const response = await fetch('{{ route('workspace.empresas.consultar-cnpj') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ cnpj: cnpj })
        });
        
        const data = await response.json();
        
        if (data.success) {
            preencherDados(data.dados);
            mostrarDadosConsultados(data.dados);
        } else {
            alert('Erro: ' + data.message);
        }
    } catch (error) {
        alert('Erro ao consultar CNPJ: ' + error.message);
    } finally {
        loading.classList.add('hidden');
        btnConsultar.disabled = false;
    }
}

function preencherDados(dados) {
    if (dados.razao_social) {
        document.getElementById('nome').value = dados.razao_social;
    }
    
    if (dados.email && dados.email !== 'null') {
        document.getElementById('email').value = dados.email;
    }
    
    if (dados.ddd_telefone_1) {
        document.getElementById('telefone').value = dados.ddd_telefone_1;
    }
    
    // Endereço
    let endereco = '';
    if (dados.logradouro) endereco += dados.logradouro;
    if (dados.numero) endereco += ', ' + dados.numero;
    if (dados.bairro) endereco += ', ' + dados.bairro;
    if (dados.municipio) endereco += ', ' + dados.municipio;
    if (dados.uf) endereco += ' - ' + dados.uf;
    if (dados.cep) endereco += ', CEP: ' + dados.cep;
    
    if (endereco) {
        document.getElementById('endereco').value = endereco;
    }
}

function mostrarDadosConsultados(dados) {
    const dadosDiv = document.getElementById('dadosReceita');
    const dadosConsultados = document.getElementById('dadosConsultados');
    
    dadosDiv.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong>Razão Social:</strong><br>${dados.razao_social || 'Não informado'}</p>
            </div>
            <div>
                <p><strong>Nome Fantasia:</strong><br>${dados.nome_fantasia || 'Não informado'}</p>
            </div>
            <div>
                <p><strong>Situação:</strong><br>${dados.descricao_situacao_cadastral || 'Não informado'}</p>
            </div>
            <div>
                <p><strong>Capital Social:</strong><br>R$ ${dados.capital_social ? parseFloat(dados.capital_social).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '0,00'}</p>
            </div>
        </div>
    `;
    
    dadosConsultados.classList.remove('hidden');
}
</script>
@endsection