<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloudConta - Sistema Contábil Multi-Empresa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2D1B69;
            --primary-light: #4C3B9F;
            --accent: #5B67CA;
            --success: #10B981;
            --warning: #F59E0B;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--accent) 100%);
        }
        .card-gradient {
            background: linear-gradient(145deg, #3A2B7A 0%, var(--primary) 100%);
        }
        .btn-success {
            background-color: var(--success);
        }
        .btn-success:hover {
            background-color: #059669;
        }
        .btn-accent {
            background-color: var(--accent);
        }
        .btn-accent:hover {
            background-color: #4338CA;
        }
        
        body::before {
            content: '';
            background-size: 75%;
            background-repeat: no-repeat;
            background-position: center center;
            opacity: 0.25;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            animation: slide 7s ease-in-out infinite;
        }
        
        @keyframes slide {
            0% { transform: translateX(-200%); opacity: 0; }
            10% { opacity: 0.20; }
            90% { opacity: 0.20; }
            100% { transform: translateX(200%); opacity: 0; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
    <!-- Header -->
    <header class="relative z-10">
        <nav class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calculator text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold">CloudConta</h1>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#servicos" class="hover:text-blue-300 transition-colors">Serviços</a>
                    <a href="#segmentos" class="hover:text-blue-300 transition-colors">Funcionalidades</a>
                    <a href="#contato" class="hover:text-blue-300 transition-colors">Contato</a>
                </div>
                
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="btn-success px-6 py-2 rounded-lg font-semibold transition-all hover:bg-green-600">
                        <i class="fas fa-sign-in-alt mr-2"></i>Entrar
                    </a>
                    <button onclick="alert('Sistema em desenvolvimento - Entre em contato!')" class="bg-white/10 px-6 py-2 rounded-lg font-semibold hover:bg-white/20 transition-all border border-white/20">
                        Cadastrar
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="container mx-auto px-6 py-20 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                Sistema Contábil 
                <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">
                    Multi-Empresa
                </span>
                na Nuvem
            </h1>
            
            <p class="text-xl md:text-2xl text-blue-100 mb-8 leading-relaxed">
                Plataforma completa com importação automática de extratos bancários, 
                conversão inteligente e gestão multi-empresa. Seus dados sempre seguros.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="{{ route('login') }}" class="btn-success px-8 py-4 rounded-xl font-bold text-lg hover:bg-green-600 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-rocket mr-3"></i>Começar Agora
                </a>
                
                <button onclick="alert('Demonstração: anderson@dicon.com.br | senha: 123456')" class="border-2 border-white/30 px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition-all">
                    <i class="fas fa-play mr-3"></i>Ver Demonstração
                </button>
            </div>
            
            <div class="flex flex-wrap justify-center gap-6 text-sm">
                <div class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <i class="fas fa-check-circle text-emerald-400 mr-2"></i>
                    100% Online
                </div>
                <div class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <i class="fas fa-building text-blue-400 mr-2"></i>
                    Multi-Empresa
                </div>
                <div class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <i class="fas fa-shield-alt text-purple-400 mr-2"></i>
                    Dados Seguros
                </div>
            </div>
        </div>
    </section>
    <!-- Nossos Serviços -->
    <section id="servicos" class="container mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-6">Nossas Ferramentas</h2>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Soluções completas para contabilidade e gestão empresarial
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Importação CEF -->
            <div class="card-gradient p-8 rounded-2xl border border-white/10 hover:border-white/20 transition-all hover:transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-100 to-cyan-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-university text-white text-2xl"></i>
                </div>
                
                <h3 class="text-2xl font-bold mb-4">Importação Bancária</h3>
                <p class="text-blue-100 mb-6 leading-relaxed">
                    Importe extratos bancários automaticamente. Sistema otimizado para processar arquivos Excel e CSV.
                </p>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Importação automática</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Categorização inteligente</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Detecção de CPF/CNPJ</span>
                    </li>
                </ul>
                
                <a href="{{ route('login') }}" class="block w-full bg-white/10 hover:bg-white/20 py-3 rounded-lg font-semibold transition-all border border-white/20 text-center">
                    Acessar →
                </a>
            </div>

            <!-- Plano de Contas -->
            <div class="card-gradient p-8 rounded-2xl border border-white/10 hover:border-white/20 transition-all hover:transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-100 to-pink-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-chart-pie text-white text-2xl"></i>
                </div>
                
                <h3 class="text-2xl font-bold mb-4">Plano de Contas</h3>
                <p class="text-blue-100 mb-6 leading-relaxed">
                    Gerencie seu plano de contas com interface intuitiva. Cadastre, edite e organize suas contas contábeis.
                </p>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Interface intuitiva</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Busca inteligente</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Import/Export Excel</span>
                    </li>
                </ul>
                
                <a href="{{ route('login') }}" class="block w-full bg-white/10 hover:bg-white/20 py-3 rounded-lg font-semibold transition-all border border-white/20 text-center">
                    Gerenciar →
                </a>
            </div>

            <!-- Multi-Empresa -->
            <div class="card-gradient p-8 rounded-2xl border border-white/10 hover:border-white/20 transition-all hover:transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-r from-emerald-100 to-teal-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-building text-white text-2xl"></i>
                </div>
                
                <h3 class="text-2xl font-bold mb-4">Sistema Multi-Empresa</h3>
                <p class="text-blue-100 mb-6 leading-relaxed">
                    Gerencie múltiplas empresas em uma única plataforma. Dados isolados e seguros para cada empresa.
                </p>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Empresas ilimitadas</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Dados isolados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-emerald-400 mr-3"></i>
                        <span>Troca rápida</span>
                    </li>
                </ul>
                
                <a href="{{ route('login') }}" class="block w-full bg-white/10 hover:bg-white/20 py-3 rounded-lg font-semibold transition-all border border-white/20 text-center">
                    Configurar →
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container mx-auto px-6 py-20">
        <div class="text-center bg-gradient-to-r from-purple-600/20 to-blue-600/20 backdrop-blur-sm p-12 rounded-3xl border border-white/10">
            <h2 class="text-4xl font-bold mb-6">
                Pronto para Começar?
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Sistema completo, seguro e fácil de usar. Seus dados contábeis organizados e acessíveis de qualquer lugar.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="btn-success px-8 py-4 rounded-xl font-bold text-lg hover:bg-green-600 transition-all shadow-lg">
                    <i class="fas fa-rocket mr-3"></i>Acessar Sistema
                </a>
                <button onclick="alert('Entre em contato: anderson@dicon.cnt.br | WhatsApp: (51) 99937-076')" class="border-2 border-white/30 px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition-all">
                    <i class="fas fa-phone mr-3"></i>Falar com Vendas
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black/20 backdrop-blur-sm border-t border-white/10" id="contato">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold">CloudConta</h3>
                    </div>
                    <p class="text-blue-100 mb-6">
                        Sistema contábil multi-empresa na nuvem. 
                        Seus dados seguros e acessíveis.
                    </p>
                    <a href="{{ route('login') }}" class="btn-accent px-6 py-2 rounded-lg font-semibold hover:bg-purple-600 transition-all inline-block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Acessar Sistema
                    </a>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Funcionalidades</h4>
                    <ul class="space-y-2 text-blue-100">
                        <li>Importação Bancária</li>
                        <li>Plano de Contas</li>
                        <li>Multi-Empresas</li>
                        <li>Relatórios</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Sistema</h4>
                    <ul class="space-y-2 text-blue-100">
                        <li>100% Online</li>
                        <li>Dados Seguros</li>
                        <li>Backup Automático</li>
                        <li>Suporte Técnico</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Contato</h4>
                    <div class="space-y-3">
                        <p class="text-green-500">
                            <i class="fas fa-envelope mr-2"></i>
                            anderson@dicon.cnt.br
                        </p>
                        <p class="text-green-500">
                            <i class="fas fa-phone mr-2"></i>
                            (51) 99937-076
                        </p>
                        <p class="text-blue-100">
                            <i class="fas fa-clock mr-2"></i>
                            Seg-Sex: 8h-18h
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 mt-12 pt-8 text-center text-blue-100">
                <p>© 2025 CloudConta - Sistema Contábil Laravel. Desenvolvido por Anderson Muller.</p>
            </div>
        </div>
    </footer>
    
    <!-- Botão flutuante WhatsApp -->
    <a href="https://web.whatsapp.com/send?phone=555151999937076&text=Olá,%20gostaria%20de%20saber%20mais%20sobre%20o%20CloudConta" 
       target="_blank" 
       class="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-all z-50 hover:scale-110">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>

    <!-- JavaScript -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        document.querySelectorAll('.card-gradient').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
