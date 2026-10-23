<?php
$tituloPagina = 'LogTec - Entregas em Tempo Real e Gestão Logística';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section Principal -->
<section class="relative overflow-hidden bg-gradient-to-b from-blue-900 via-slate-900 to-slate-950 text-white py-16 sm:py-24">
    <!-- Padrão de grade sutil de fundo -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Coluna de Texto e Call-to-Action -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-400/20 text-blue-300 text-xs sm:text-sm font-semibold backdrop-blur-sm">
                    <span class="pulse-indicator"></span>
                    Logística Inteligente & Entregas em Tempo Real
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15]">
                    Facilidade de entrada para <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-blue-400">ambas as partes</span>.
                </h1>

                <p class="text-slate-300 text-base sm:text-lg max-w-2xl leading-relaxed mx-auto lg:mx-0">
                    Conectamos <strong>Empresas</strong> que precisam enviar mercadorias e <strong>Funcionários/Entregadores</strong> à procura de emprego. Gestão completa de rotas, pedidos pendentes e templates organizacionais.
                </p>

                <!-- Botões de Ação para as duas maneiras de entrar -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="login.php?tipo=empresa" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-center shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2.5">
                        <i class="fa-solid fa-building text-base"></i>
                        <span>Entrar como Empresa (CNPJ)</span>
                    </a>
                    <a href="login.php?tipo=funcionario" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-100 font-bold text-center transition flex items-center justify-center gap-2.5">
                        <i class="fa-solid fa-id-card text-emerald-400 text-base"></i>
                        <span>Entrar como Entregador (CPF)</span>
                    </a>
                </div>

                <!-- Garantia de Maioridade e Segurança -->
                <div class="flex items-center justify-center lg:justify-start gap-6 pt-3 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                        <span>Segurança com PDO</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-blue-400"></i>
                        <span>Apenas Maiores de 18 Anos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-sky-400"></i>
                        <span>CPF Único</span>
                    </div>
                </div>
            </div>

            <!-- Card Visual Interativo: Simulação de Pedido e Rastreio -->
            <div class="lg:col-span-5">
                <div class="bg-slate-800/80 backdrop-blur-md rounded-2xl border border-slate-700/80 p-6 shadow-2xl relative overflow-hidden">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-700/60 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <span class="text-xs font-mono uppercase text-slate-300 font-bold">Entrega em Andamento</span>
                        </div>
                        <span class="text-xs font-mono bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded border border-blue-400/20">LOG-2026-9813</span>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-emerald-400"><i class="fa-solid fa-circle-dot"></i></div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Origem do Pedido</p>
                                <p class="font-semibold text-white">Hub LogTec - Av. Paulista, 1000</p>
                            </div>
                        </div>

                        <div class="border-l-2 border-dashed border-slate-600 ml-2 pl-4 py-1">
                            <div class="inline-flex items-center gap-2 text-xs text-sky-400 bg-sky-950/60 px-2.5 py-1 rounded-lg">
                                <i class="fa-solid fa-motorcycle animate-bounce"></i>
                                <span>Entregador em trânsito (35 min restantes)</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-rose-400"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Destino de Entrega</p>
                                <p class="font-semibold text-white">Rua Vergueiro, 3185 - Vila Mariana</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400">Valor do Frete</span>
                            <p class="text-xl font-extrabold text-emerald-400">R$ 32,00</p>
                        </div>
                        <a href="rotas.php" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                            Ver Rota no Mapa <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Seção dos 4 Objetivos Principais do requisitos.md -->
<section class="py-16 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Objetivos Centrais</h2>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">Projetado para revolucionar a logística</p>
            <p class="text-slate-500 mt-3 text-sm sm:text-base">Todas as metas estabelecidas no requisitos.md implementadas com robustez e fluidez.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Objetivo 1 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-400 hover:shadow-lg hover:shadow-blue-500/5 transition group">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Facilitação de Entrada</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Acesso customizado para ambas as partes: empresas com <strong>CNPJ</strong> e entregadores/funcionários com <strong>CPF único</strong> e verificação de maioridade.
                </p>
            </div>

            <!-- Objetivo 2 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-emerald-400 hover:shadow-lg hover:shadow-emerald-500/5 transition group">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Entregas em Tempo Real</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Acompanhamento simultâneo do status das entregas: do momento da postagem até a confirmação de recebimento no cliente final.
                </p>
            </div>

            <!-- Objetivo 3 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-purple-400 hover:shadow-lg hover:shadow-purple-500/5 transition group">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-route"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Rotas de Entrega</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Visualização em mapa interativo, cálculo de trajetos, previsão estimada de chegada e controle de pontos de parada para motoristas.
                </p>
            </div>

            <!-- Objetivo 4 -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-amber-400 hover:shadow-lg hover:shadow-amber-500/5 transition group">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Pedidos Pendentes</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Painel intuitivo com fila de pedidos pendentes para rápida aceitação por entregadores livres e despacho otimizado pelas empresas.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- Destaque do Requisito de Templates de Listas Personalizáveis -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <div class="lg:col-span-5 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-100/70 px-3 py-1 rounded-full">
                    Recurso Core do Sistema
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Templates de Listas com Ícones Alteráveis
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    O sistema disponibiliza vários templates prontos: <strong>listas de mercado</strong>, <strong>listas de músicas</strong>, <strong>filmes</strong>, <strong>viagens</strong> e rotas operacionais. Cada uma com seu ícone customizável pelo próprio usuário.
                </p>
                <div class="pt-2">
                    <a href="listas.php" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-md shadow-amber-500/20 transition">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Explorar Meus Templates de Listas</span>
                    </a>
                </div>
            </div>

            <!-- Demonstração dos Templates Solicitados -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Lista de Mercado</h4>
                        <p class="text-xs text-slate-500 mt-1">Compras da semana, itens essenciais e mantimentos para rotas longas.</p>
                        <span class="inline-block mt-2 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Ícone: Carrinho</span>
                    </div>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Lista de Músicas</h4>
                        <p class="text-xs text-slate-500 mt-1">Playlists, álbuns e podcasts para acompanhar os trajetos do entregador.</p>
                        <span class="inline-block mt-2 text-[11px] font-semibold text-violet-700 bg-violet-50 px-2 py-0.5 rounded">Ícone: Nota Musical</span>
                    </div>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-film"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Lista de Filmes</h4>
                        <p class="text-xs text-slate-500 mt-1">Títulos favoritos e recomendações para relaxar nos momentos de descanso.</p>
                        <span class="inline-block mt-2 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Ícone: Rolo de Filme</span>
                    </div>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-base">Lista de Viagens</h4>
                        <p class="text-xs text-slate-500 mt-1">Checklist de viagens rodoviárias, manutenções do veículo e destinos.</p>
                        <span class="inline-block mt-2 text-[11px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded">Ícone: Avião / Rota</span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- CTA Final -->
<section class="py-14 bg-gradient-to-r from-blue-700 to-sky-600 text-white text-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <h2 class="text-3xl font-extrabold tracking-tight">Pronto para começar na LogTec?</h2>
        <p class="text-blue-100 mt-3 text-base sm:text-lg">Cadastre-se gratuitamente agora e tenha acesso imediato a todas as funcionalidades.</p>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="cadastro.php" class="w-full sm:w-auto px-8 py-3.5 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl shadow-lg transition">
                Criar Minha Conta Grátis
            </a>
            <a href="login.php" class="w-full sm:w-auto px-8 py-3.5 bg-blue-800/60 hover:bg-blue-800 text-white font-bold rounded-xl border border-blue-400/30 transition">
                Já Possuo Conta
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
