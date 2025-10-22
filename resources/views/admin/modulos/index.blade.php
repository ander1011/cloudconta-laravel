@extends('layouts.app')

@section('title', 'Módulos Disponíveis')

@section('content')
<div class="min-h-screen admin-gradient py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🎯 Módulos Disponíveis</h1>
                    <p class="text-gray-600 mt-2">Sistema completo modular para gestão empresarial</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.modulos.empresas') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-building mr-2"></i>Gerenciar Empresas
                    </a>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Grid de Módulos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($modulos as $modulo)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-2xl">
                <!-- Cabeçalho colorido -->
                <div class="h-32 bg-gradient-to-br from-blue-500 to-purple-600 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative h-full flex items-center justify-center">
                        <div class="text-6xl text-white opacity-90">
                            <i class="{{ $modulo->icone }}"></i>
                        </div>
                    </div>
                    @if($modulo->ativo)
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                            ATIVO
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Corpo do Card -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $modulo->nome }}</h3>
                    <p class="text-gray-600 text-sm mb-4 h-20">{{ $modulo->descricao }}</p>
                    
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-600">Preço Mensal:</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $modulo->preco_formatado }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Ordem:</span>
                            <span class="font-semibold">#{{ $modulo->ordem }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Estatísticas -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-2xl">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total de Módulos</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $modulos->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-2xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Módulos Ativos</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $modulos->where('ativo', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 text-2xl">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Valor Total (Todos)</p>
                        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($modulos->sum('preco_mensal'), 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela Detalhada -->
        <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">📋 Detalhamento de Módulos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Módulo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Descrição</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Preço</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($modulos as $modulo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900">{{ $modulo->ordem }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-lg">
                                        <i class="{{ $modulo->icone }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $modulo->nome }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $modulo->slug }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ Str::limit($modulo->descricao, 60) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-lg font-bold text-blue-600">{{ $modulo->preco_formatado }}</span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($modulo->ativo)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Ativo
                                </span>
                                @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inativo
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
