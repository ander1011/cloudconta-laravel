@extends('layouts.app')

@section('title', 'Gestão de Módulos - Empresas')

@section('content')
<div class="min-h-screen admin-gradient py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🎛️ Gestão de Módulos por Empresa</h1>
                    <p class="text-gray-600 mt-2">Ative ou desative módulos para cada empresa</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.modulos.index') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-cube mr-2"></i>Ver Módulos
                    </a>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Lista de Empresas -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">
                    📋 Empresas Cadastradas ({{ $empresas->count() }})
                </h2>
            </div>

            @if($empresas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Empresa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    CNPJ
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Módulos Ativos
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($empresas as $empresa)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($empresa->nome, 0, 2)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $empresa->nome }}
                                            </div>
                                            @if($empresa->razao_social)
                                            <div class="text-sm text-gray-500">
                                                {{ $empresa->razao_social }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm text-gray-900">
                                        {{ $empresa->cnpj_formatado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                                            {{ $empresa->modulos->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $empresa->modulos->count() }}/8
                                        </span>
                                        @if($empresa->modulos->count() > 0)
                                        <div class="flex -space-x-2">
                                            @foreach($empresa->modulos->take(3) as $modulo)
                                            <div class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs border-2 border-white"
                                                 title="{{ $modulo->nome }}">
                                                <i class="{{ $modulo->icone }}"></i>
                                            </div>
                                            @endforeach
                                            @if($empresa->modulos->count() > 3)
                                            <div class="h-8 w-8 rounded-full bg-gray-500 text-white flex items-center justify-center text-xs border-2 border-white">
                                                +{{ $empresa->modulos->count() - 3 }}
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.modulos.gerenciar-empresa', $empresa->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-lg transition-all transform hover:scale-105">
                                        <i class="fas fa-cog mr-2"></i>
                                        Gerenciar Módulos
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhuma empresa cadastrada</h3>
                    <p class="text-gray-500">Cadastre empresas primeiro para gerenciar seus módulos</p>
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
