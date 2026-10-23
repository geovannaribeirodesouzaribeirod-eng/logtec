<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../auth.php';

$usuarioLogado = usuarioLogado();
$tipoUsuario = tipoUsuario();
$dadosUsuario = getUsuarioAtual();
$tituloPagina = $tituloPagina ?? 'LogTec - Entregas e Rotas em Tempo Real';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Leaflet.js CSS (Rotas e Mapas em tempo real) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="flex flex-col min-h-full text-slate-800 antialiased bg-slate-50">

    <!-- Navegação Principal MobileFirst & Responsiva -->
    <header class="sticky top-0 z-40 border-b border-slate-200/80 glass-nav shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Marca -->
                <div class="flex items-center gap-3">
                    <a href="index.php" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-sky-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-truck-fast text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-1">
                                Log<span class="text-blue-600">Tec</span>
                            </span>
                            <span class="hidden sm:block text-[10px] uppercase font-bold tracking-wider text-slate-400 -mt-1">
                                Entregas em Tempo Real
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Links Desktop -->
                <nav class="hidden md:flex items-center gap-1 font-medium text-sm text-slate-600">
                    <a href="index.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                        <i class="fa-solid fa-house mr-1.5 text-xs"></i> Início
                    </a>

                    <?php if ($usuarioLogado): ?>
                        <a href="dashboard.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                            <i class="fa-solid fa-chart-pie mr-1.5 text-xs"></i> Painel
                        </a>
                        <a href="rotas.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                            <i class="fa-solid fa-map-location-dot mr-1.5 text-xs text-emerald-500"></i> Rotas & Pedidos
                        </a>
                        <a href="listas.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                            <i class="fa-solid fa-list-check mr-1.5 text-xs text-amber-500"></i> Templates de Listas
                        </a>
                        <a href="vagas.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                            <i class="fa-solid fa-briefcase mr-1.5 text-xs text-purple-500"></i> Vagas
                        </a>
                    <?php else: ?>
                        <a href="listas.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                            <i class="fa-solid fa-list-check mr-1.5 text-xs text-amber-500"></i> Listas & Templates
                        </a>
                        <a href="vagas.php" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100/70 transition">
                            <i class="fa-solid fa-briefcase mr-1.5 text-xs text-purple-500"></i> Oportunidades
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Área de Autenticação / Perfil -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if ($usuarioLogado): ?>
                        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                            <div class="text-right">
                                <p class="text-xs font-bold text-slate-800 leading-tight">
                                    <?= htmlspecialchars($dadosUsuario['nome'] ?? $dadosUsuario['nome_fantasia'] ?? 'Usuário') ?>
                                </p>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid <?= $tipoUsuario === 'empresa' ? 'fa-building text-blue-600' : 'fa-id-badge text-emerald-600' ?>"></i>
                                    <?= $tipoUsuario === 'empresa' ? 'Empresa (CNPJ)' : 'Entregador (CPF)' ?>
                                </span>
                            </div>
                            <a href="logout.php" data-confirm="Deseja realmente sair da sua conta?" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Sair da Conta">
                                <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-blue-600 transition">
                            Entrar
                        </a>
                        <a href="cadastro.php" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm shadow-blue-500/20 transition">
                            Cadastre-se
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Botão Menu Mobile -->
                <div class="flex md:hidden items-center gap-2">
                    <button id="btn-mobile-menu" type="button" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition focus:outline-none" aria-label="Abrir Menu">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Menu Mobile Expansível -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2">
            <a href="index.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                <i class="fa-solid fa-house w-6 text-slate-400"></i> Início
            </a>
            <?php if ($usuarioLogado): ?>
                <a href="dashboard.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    <i class="fa-solid fa-chart-pie w-6 text-blue-500"></i> Painel Principal
                </a>
                <a href="rotas.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    <i class="fa-solid fa-map-location-dot w-6 text-emerald-500"></i> Rotas e Pedidos
                </a>
                <a href="listas.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    <i class="fa-solid fa-list-check w-6 text-amber-500"></i> Templates de Listas
                </a>
                <a href="vagas.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    <i class="fa-solid fa-briefcase w-6 text-purple-500"></i> Vagas de Emprego
                </a>
                <div class="pt-3 border-t border-slate-100">
                    <div class="mb-2 px-3">
                        <p class="text-xs font-bold text-slate-800">
                            <?= htmlspecialchars($dadosUsuario['nome'] ?? $dadosUsuario['nome_fantasia'] ?? 'Usuário') ?>
                        </p>
                        <p class="text-xs text-slate-500">
                            <?= $tipoUsuario === 'empresa' ? 'Empresa (CNPJ)' : 'Funcionário / Candidato (CPF)' ?>
                        </p>
                    </div>
                    <a href="logout.php" data-confirm="Deseja realmente sair do sistema?" class="block w-full text-left px-3 py-2 rounded-lg text-rose-600 font-semibold hover:bg-rose-50">
                        <i class="fa-solid fa-arrow-right-from-bracket w-6"></i> Sair da Sessão
                    </a>
                </div>
            <?php else: ?>
                <a href="listas.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    <i class="fa-solid fa-list-check w-6 text-amber-500"></i> Templates de Listas
                </a>
                <a href="vagas.php" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    <i class="fa-solid fa-briefcase w-6 text-purple-500"></i> Vagas de Emprego
                </a>
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <a href="login.php" class="text-center px-4 py-2 border border-slate-200 text-slate-700 font-semibold rounded-xl text-sm">
                        Entrar
                    </a>
                    <a href="cadastro.php" class="text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl text-sm">
                        Criar Conta
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Componente de Modal e Toast Container -->
    <?php require_once __DIR__ . '/toast.php'; ?>
    <?php require_once __DIR__ . '/modal.php'; ?>

    <!-- Conteúdo Dinâmico -->
    <main class="flex-1">
