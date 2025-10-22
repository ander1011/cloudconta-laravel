<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Workspace - CloudConta</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #ffffff;
            height: 100vh;
            overflow: hidden;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(196, 30, 58, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(196, 30, 58, 0.6);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(196, 30, 58, 0.8);
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: #C41E3A;
            border-right: 1px solid #A11729;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(196, 30, 58, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-toggle {
            position: absolute;
            top: 50%;
            right: -15px;
            background: #C41E3A;
            border: 2px solid #A11729;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar-toggle:hover {
            background: #A11729;
            transform: scale(1.1);
        }

        .logo-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .logo-section {
            padding: 20px 5px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .logo {
            font-size: 14px;
            writing-mode: vertical-lr;
            text-orientation: mixed;
            margin-bottom: 0;
        }

        .subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .subtitle {
            display: none;
        }

        .user-info {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .user-info {
            padding: 15px 5px;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff6b9d, #ffa07a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .user-details {
            flex: 1;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .user-details {
            display: none;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
        }

        .user-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .company-selector {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .company-selector {
            display: none;
        }

        .company-select {
            width: 100%;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
        }

        .company-select option {
            background: #C41E3A;
            color: #ffffff;
        }

        .company-label {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .add-company-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 8px 12px;
            margin-top: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px dashed rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            color: #ffffff;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .add-company-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
        }
    
        /* Navigation Menu */
        .nav-menu {
            flex: 1;
            padding: 20px 15px;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-menu {
            padding: 20px 5px;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            background: rgba(161, 23, 41, 0.8);
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
            cursor: pointer;
        }

        .sidebar.collapsed .nav-link {
            padding: 12px 8px;
            justify-content: center;
        }

        .nav-link:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
            background: rgba(161, 23, 41, 0.9);
            color: #ffffff;
        }

        .nav-link.active {
            background: #A11729;
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            fill: currentColor;
            flex-shrink: 0;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        .sub-nav {
            display: none;
            background: transparent;
            padding-left: 10px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sub-nav {
            display: none !important;
        }

        .sub-nav.show {
            display: block;
        }

        .sub-nav-link {
            display: block;
            padding: 8px 16px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
            background: rgba(161, 23, 41, 0.6);
            border-radius: 6px;
            margin-bottom: 3px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.05);
            cursor: pointer;
        }

        .sub-nav-link:hover {
            color: #ffffff;
            background: rgba(161, 23, 41, 0.8);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
            transform: translateX(3px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .header {
            background: #C41E3A;
            padding: 15px 30px;
            border-bottom: 1px solid #A11729;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(196, 30, 58, 0.3);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            color: rgba(255, 255, 255, 0.9);
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            padding: 5px;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content-area {
            flex: 1;
            padding: 30px;
            padding-bottom: 10px;
            overflow-y: auto;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .module-placeholder {
            text-align: center;
            color: #6c757d;
            max-width: 600px;
        }

        .module-placeholder h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #495057;
        }

        .module-placeholder p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .module-placeholder .icon {
            font-size: 80px;
            color: #C41E3A;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .footer {
            background: #C41E3A;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 12px;
            border-top: 1px solid #A11729;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 240px;
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.9);
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Animações para notificações */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-toggle" onclick="toggleSidebar()">
                <span id="toggle-icon">←</span>
            </div>

            <div class="logo-section">
                <div class="logo">CLOUDCONTA</div>
                <div class="subtitle">Conecte-se com quem entende o agora da contabilidade</div>
            </div>

            <div class="user-info">
                <div class="user-card">
                    <div class="user-avatar">{{ strtoupper(substr($user->nome, 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ $user->nome }}</div>
                        <div class="user-role">{{ $user->tipoUsuario }}</div>
                    </div>
                </div>
            </div>

            <div class="company-selector">
                <label class="company-label">
                    <svg style="width:16px;height:16px;margin-right:8px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
                    </svg>
                    Empresa Ativa
                </label>
                <select class="company-select" onchange="selectEmpresa(this.value)">
                    @if($empresas->count() > 0)
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" 
                                {{ isset($empresaSelecionada) && $empresaSelecionada && $empresaSelecionada->id == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nome }} - {{ $empresa->cnpj_formatado }}
                        </option>
                        @endforeach
                    @else
                        <option value="">Nenhuma empresa cadastrada</option>
                    @endif
                </select>
                @if($empresas->count() == 0)
                <a href="{{ route('workspace.empresas.create') }}" class="add-company-btn">
                    <svg style="width:14px;height:14px;margin-right:5px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                    </svg>
                    Cadastrar Empresa
                </a>
                @endif
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="javascript:void(0)" class="nav-link active" onclick="selectModule(this, 'Dashboard', 'Visão Geral')">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="javascript:void(0)" class="nav-link" onclick="toggleSubNav(event, 'financeiro-sub')">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="nav-text">Financeiro</span>
                    </a>
                    <div class="sub-nav" id="financeiro-sub">
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Financeiro', 'Contas a Pagar')">Contas a Pagar</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Financeiro', 'Contas a Receber')">Contas a Receber</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Financeiro', 'Fluxo de Caixa')">Fluxo de Caixa</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Financeiro', 'Clientes')">Clientes</a>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="javascript:void(0)" class="nav-link" onclick="toggleSubNav(event, 'contabilidade-sub')">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                        </svg>
                        <span class="nav-text">Contabilidade</span>
                    </a>
                    <div class="sub-nav" id="contabilidade-sub">
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Contabilidade', 'Plano de Contas')">Plano de Contas</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Contabilidade', 'Lançamentos')">Lançamentos</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Contabilidade', 'Balancetes')">Balancetes</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Contabilidade', 'DRE')">DRE</a>
                    </div>
                </div>

                <div class="nav-item">
                    <a href="javascript:void(0)" class="nav-link" onclick="toggleSubNav(event, 'extratores-sub')">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z M9,13V19H7V13H9M13,7V19H11V7H13M17,11V19H15V11H17Z"/>
                        </svg>
                        <span class="nav-text">Extratores</span>
                    </a>
                    <div class="sub-nav" id="extratores-sub">
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Extratores', 'Extrator Bancário')">Extrator Bancário</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Extratores', 'Extrator NF-e')">Extrator NF-e</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Extratores', 'Extrator SPED')">Extrator SPED</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Extratores', 'Extrator Cartão')">Extrator Cartão</a>
                    </div>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('workspace.empresas.index') }}" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                        </svg>
                        <span class="nav-text">Gestão de Empresas</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="javascript:void(0)" class="nav-link" onclick="toggleSubNav(event, 'config-sub')">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                        </svg>
                        <span class="nav-text">Configurações</span>
                    </a>
                    <div class="sub-nav" id="config-sub">
                        @if($stats['can_manage_users'])
                        <a href="{{ route('admin.users.index') }}" class="sub-nav-link">Usuários</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Configurações', 'Permissões')">Permissões</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Configurações', 'Sistema')">Sistema</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Configurações', 'Backup')">Backup</a>
                        @else
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Configurações', 'Perfil')">Meu Perfil</a>
                        <a href="javascript:void(0)" class="sub-nav-link" onclick="selectModule(this, 'Configurações', 'Preferências')">Preferências</a>
                        @endif
                    </div>
                </div>
            </nav>
        </div>    
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="breadcrumb">
                    <span id="breadcrumb-content">Dashboard > Visão Geral</span>
                </div>
                <div class="header-actions">
                    <button class="notification-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <span class="notification-badge">{{ $stats['users_total'] ?? 0 }}</span>
                    </button>
                    <span>Último acesso: {{ now()->format('d/m/Y H:i') }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="module-placeholder">
                    <div class="icon">📊</div>
                    <h2>Área de Trabalho CloudConta</h2>
                    <p>Sistema contábil completo com {{ $stats['users_total'] }} usuários cadastrados.</p>
                    <p>Navegue pelo menu lateral para acessar os módulos do sistema.</p>
                    <p><strong>Módulo selecionado:</strong> <span id="current-module">Dashboard > Visão Geral</span></p>
                    
                    <!-- Estatísticas Rápidas -->
                    <!-- Estatísticas Baseadas em Permissões -->
                    <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
                        @if($stats['can_manage_users'])
                        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); color: #333;">
                            <h3 style="color: #C41E3A; margin-bottom: 10px;">👥 Usuários</h3>
                            <p style="font-size: 24px; font-weight: bold; margin: 0;">{{ $stats['users_total'] }}</p>
                            <small style="color: #666;">{{ $stats['users_active'] }} ativos</small>
                        </div>
                        @endif
                        
                        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); color: #333;">
                            <h3 style="color: #C41E3A; margin-bottom: 10px;">🏢 Empresas</h3>
                            <p style="font-size: 24px; font-weight: bold; margin: 0;">{{ count($empresas) }}</p>
                            <small style="color: #666;">Disponíveis</small>
                        </div>
                        
                        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); color: #333;">
                            <h3 style="color: #C41E3A; margin-bottom: 10px;">⚙️ Sistema</h3>
                            <p style="font-size: 24px; font-weight: bold; margin: 0; color: #28a745;">Online</p>
                            <small style="color: #666;">Funcionando</small>
                        </div>
                        
                        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); color: #333;">
                            <h3 style="color: #C41E3A; margin-bottom: 10px;">👤 Acesso</h3>
                            <p style="font-size: 24px; font-weight: bold; margin: 0; color: {{ $user->isAdmin() ? '#dc3545' : '#007bff' }};">
                                {{ $user->isAdmin() ? 'Admin' : 'Usuário' }}
                            </p>
                            <small style="color: #666;">{{ $user->nome }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                CloudConta © 2025 - Conecte-se com quem entende o agora da contabilidade
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleIcon = document.getElementById('toggle-icon');
            
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.textContent = '→';
            } else {
                toggleIcon.textContent = '←';
            }
        }

        // Sub navigation toggle
        function toggleSubNav(event, subNavId) {
            event.preventDefault();
            
            const clickedLink = event.target.closest('.nav-link');
            const subNav = document.getElementById(subNavId);
            const sidebar = document.getElementById('sidebar');
            
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
                document.getElementById('toggle-icon').textContent = '←';
            }
            
            const isCurrentlyOpen = subNav.classList.contains('show');
            
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            document.querySelectorAll('.sub-nav').forEach(subNavElement => {
                subNavElement.classList.remove('show');
            });
            
            if (!isCurrentlyOpen) {
                clickedLink.classList.add('active');
                subNav.classList.add('show');
            }
        }

        // Module selection functionality
        function selectModule(element, module, subModule) {
            const sidebar = document.getElementById('sidebar');
            
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
                document.getElementById('toggle-icon').textContent = '←';
            }
            
            const breadcrumb = document.getElementById('breadcrumb-content');
            const currentModule = document.getElementById('current-module');
            
            const breadcrumbText = `${module} > ${subModule}`;
            breadcrumb.textContent = breadcrumbText;
            currentModule.textContent = breadcrumbText;
            
            document.querySelectorAll('.nav-link, .sub-nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            element.classList.add('active');
            
            if (element.classList.contains('sub-nav-link')) {
                const parentNav = element.closest('.nav-item').querySelector('.nav-link');
                parentNav.classList.add('active');
            }
        }

        // Empresa selection
        function selectEmpresa(empresaId) {
            const select = document.querySelector('.company-select');
            const originalBg = select.style.background;
            
            // Visual feedback
            select.style.background = 'rgba(255, 255, 255, 0.2)';
            select.disabled = true;
            
            fetch('{{ route("workspace.select-empresa") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    empresa_id: empresaId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Feedback visual de sucesso
                    select.style.background = 'rgba(76, 175, 80, 0.3)';
                    
                    // Mostrar notificação
                    const notification = document.createElement('div');
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: #4CAF50;
                        color: white;
                        padding: 15px 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                        z-index: 9999;
                        animation: slideInRight 0.3s ease;
                    `;
                    notification.innerHTML = `
                        <strong>✓ Empresa selecionada!</strong><br>
                        <small>${data.empresa.nome}</small>
                    `;
                    document.body.appendChild(notification);
                    
                    // Remover notificação após 3 segundos
                    setTimeout(() => {
                        notification.style.animation = 'slideOutRight 0.3s ease';
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);
                    
                    // Resetar visual após 1 segundo
                    setTimeout(() => {
                        select.style.background = originalBg;
                        select.disabled = false;
                    }, 1000);
                    
                    console.log('Empresa selecionada:', data.empresa);
                } else {
                    // Erro
                    select.style.background = 'rgba(244, 67, 54, 0.3)';
                    alert('Erro: ' + data.message);
                    setTimeout(() => {
                        select.style.background = originalBg;
                        select.disabled = false;
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Erro ao selecionar empresa:', error);
                select.style.background = 'rgba(244, 67, 54, 0.3)';
                alert('Erro ao selecionar empresa. Tente novamente.');
                setTimeout(() => {
                    select.style.background = originalBg;
                    select.disabled = false;
                }, 1000);
            });
        }
    </script>
</body>
</html>
    
    
    