<?php
$tituloPagina = 'Rotas e Entregas em Tempo Real - LogTec';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/bd.php';

$tipo = tipoUsuario();
$usuario = getUsuarioAtual();
$pedidoIdFoco = intval($_GET['pedido'] ?? 0);

// Busca pedidos para visualização no mapa
if ($pedidoIdFoco > 0) {
    $pedidoAtivo = dbFetchOne("SELECT p.*, e.nome_fantasia as empresa_nome, u.nome as entregador_nome 
                               FROM pedidos p 
                               LEFT JOIN empresas e ON p.empresa_id = e.id 
                               LEFT JOIN usuarios u ON p.usuario_id = u.id 
                               WHERE p.id = ?", [$pedidoIdFoco]);
} else {
    // Pega o pedido mais recente em trânsito ou pendente
    $pedidoAtivo = dbFetchOne("SELECT p.*, e.nome_fantasia as empresa_nome, u.nome as entregador_nome 
                               FROM pedidos p 
                               LEFT JOIN empresas e ON p.empresa_id = e.id 
                               LEFT JOIN usuarios u ON p.usuario_id = u.id 
                               WHERE p.status IN ('em_transito', 'pendente') 
                               ORDER BY p.id DESC LIMIT 1");
}

$todosPedidos = dbFetchAll("SELECT p.*, e.nome_fantasia as empresa_nome 
                            FROM pedidos p 
                            LEFT JOIN empresas e ON p.empresa_id = e.id 
                            ORDER BY p.id DESC LIMIT 15");
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="pulse-indicator"></span>
                <span class="text-xs uppercase tracking-wider font-bold text-slate-500">Monitoramento GPS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Rotas de Entrega em Tempo Real
            </h1>
            <p class="text-sm text-slate-500">Visualização geográfica de trajetos, pontos de origem e destinos com simulação ao vivo.</p>
        </div>

        <div class="flex items-center gap-3">
            <button id="btn-simular-movimento" type="button" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-play"></i>
                <span>Simular Movimento do Entregador</span>
            </button>
        </div>
    </div>

    <!-- Layout Grid: Mapa + Painel Lateral -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Mapa Interativo Leaflet (8 Colunas em telas grandes) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-4">
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                    <span class="inline-flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Origem (Hub)</span>
                    <span class="inline-flex items-center gap-1 ml-3"><span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span> Destino (Cliente)</span>
                    <span class="inline-flex items-center gap-1 ml-3"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Entregador GPS</span>
                </div>
                <div id="status-simulacao" class="text-xs font-mono font-bold text-slate-400">
                    GPS Conectado
                </div>
            </div>

            <!-- Container do Mapa -->
            <div id="mapa-rotas" class="rounded-xl overflow-hidden border border-slate-200 shadow-inner"></div>

            <!-- Dados da Rota Selecionada -->
            <?php if ($pedidoAtivo): ?>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="text-slate-400 uppercase font-bold text-[10px]">Origem (Coleta)</span>
                        <p class="font-bold text-slate-800"><?= htmlspecialchars($pedidoAtivo['origem_endereco']) ?></p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold text-[10px]">Destino (Entrega)</span>
                        <p class="font-bold text-slate-800"><?= htmlspecialchars($pedidoAtivo['destino_endereco']) ?></p>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold text-[10px]">Status & Valor</span>
                        <p class="font-bold text-emerald-600 text-base">
                            R$ <?= number_format($pedidoAtivo['valor_frete'], 2, ',', '.') ?>
                            <span class="text-xs font-normal text-slate-500">(<?= htmlspecialchars($pedidoAtivo['prazo_estimado']) ?>)</span>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Painel Lateral: Seletor de Pedidos & Ações (4 Colunas) -->
        <div class="lg:col-span-4 space-y-4">
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-list-ul text-blue-600"></i> Pedidos e Rotas
                    </h3>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                        <?= count($todosPedidos) ?>
                    </span>
                </div>

                <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                    <?php if (empty($todosPedidos)): ?>
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Nenhum pedido encontrado.
                        </div>
                    <?php else: ?>
                        <?php foreach ($todosPedidos as $p): ?>
                            <?php $isFoco = ($pedidoAtivo && $pedidoAtivo['id'] === $p['id']); ?>
                            <a href="rotas.php?pedido=<?= $p['id'] ?>" class="block p-3.5 transition <?= $isFoco ? 'bg-blue-50/80 border-l-4 border-blue-600' : 'hover:bg-slate-50' ?>">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-mono font-bold text-slate-800"><?= htmlspecialchars($p['codigo_rastreio']) ?></span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $p['status'] === 'em_transito' ? 'bg-blue-100 text-blue-700' : ($p['status'] === 'entregue' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                    </span>
                                </div>
                                <p class="text-xs font-medium text-slate-700 truncate"><?= htmlspecialchars($p['cliente_nome']) ?></p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">Destino: <?= htmlspecialchars($p['destino_endereco']) ?></p>
                                <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-slate-100 text-[11px]">
                                    <span class="text-slate-400"><?= htmlspecialchars($p['prazo_estimado']) ?></span>
                                    <span class="font-extrabold text-emerald-600">R$ <?= number_format($p['valor_frete'], 2, ',', '.') ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Instruções -->
            <div class="p-4 bg-gradient-to-br from-blue-900 to-slate-900 text-white rounded-2xl shadow-md text-xs space-y-2">
                <div class="flex items-center gap-2 font-bold text-sky-300">
                    <i class="fa-solid fa-satellite-dish"></i>
                    <span>Rastreio Conectado</span>
                </div>
                <p class="text-slate-300 leading-relaxed">
                    Clique em <strong>"Simular Movimento"</strong> para observar a moto do entregador se deslocando em tempo real com atualização de telemetria até o destino.
                </p>
            </div>

        </div>

    </div>

</div>

<!-- Script do Mapa Leaflet e Simulação em Tempo Real -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Coordenadas base do pedido ativo ou padrão em SP
    const origemLat = <?= $pedidoAtivo['origem_lat'] ?? -23.550520 ?>;
    const origemLng = <?= $pedidoAtivo['origem_lng'] ?? -46.633308 ?>;
    const destinoLat = <?= $pedidoAtivo['destino_lat'] ?? -23.561414 ?>;
    const destinoLng = <?= $pedidoAtivo['destino_lng'] ?? -46.655881 ?>;

    const map = L.map('mapa-rotas').setView([origemLat, origemLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Ícone de Origem (Empresa/Hub)
    const iconeOrigem = L.divIcon({
        className: 'custom-pin',
        html: `<div style="background-color:#2563eb; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 4px 6px rgba(0,0,0,0.3); font-size:14px;"><i class="fa-solid fa-warehouse"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    // Ícone de Destino (Cliente)
    const iconeDestino = L.divIcon({
        className: 'custom-pin',
        html: `<div style="background-color:#e11d48; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 4px 6px rgba(0,0,0,0.3); font-size:14px;"><i class="fa-solid fa-location-dot"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    // Ícone do Entregador (Moto em Movimento)
    const iconeEntregador = L.divIcon({
        className: 'custom-pin-moto',
        html: `<div style="background-color:#10b981; color:white; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid white; box-shadow:0 0 12px rgba(16,185,129,0.8); font-size:16px;"><i class="fa-solid fa-motorcycle"></i></div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    // Marcadores
    L.marker([origemLat, origemLng], { icon: iconeOrigem })
        .addTo(map)
        .bindPopup(`<b>Ponto de Coleta</b><br><?= addslashes($pedidoAtivo['origem_endereco'] ?? 'Hub Central') ?>`);

    L.marker([destinoLat, destinoLng], { icon: iconeDestino })
        .addTo(map)
        .bindPopup(`<b>Destino Final</b><br><?= addslashes($pedidoAtivo['destino_endereco'] ?? 'Cliente') ?>`);

    // Linha da Rota Traçada
    const rotaPolyline = L.polyline([
        [origemLat, origemLng],
        [(origemLat + destinoLat) / 2 + 0.002, (origemLng + destinoLng) / 2 - 0.003],
        [destinoLat, destinoLng]
    ], {
        color: '#0284c7',
        weight: 5,
        opacity: 0.8,
        dashArray: '8, 8'
    }).addTo(map);

    map.fitBounds(rotaPolyline.getBounds(), { padding: [50, 50] });

    // Marcador dinâmico do entregador
    let markerEntregador = L.marker([origemLat, origemLng], { icon: iconeEntregador }).addTo(map);

    // Lógica da Simulação de Movimento
    const btnSimular = document.getElementById('btn-simular-movimento');
    const statusTxt = document.getElementById('status-simulacao');
    let simulando = false;
    let step = 0;
    const totalSteps = 60;

    btnSimular.addEventListener('click', () => {
        if (simulando) return;
        simulando = true;
        step = 0;
        btnSimular.disabled = true;
        btnSimular.classList.add('opacity-50');
        statusTxt.innerHTML = '<span class="text-emerald-600 font-bold"><i class="fa-solid fa-spinner fa-spin"></i> Em Movimento...</span>';
        Toast.info('Simulação iniciada: entregador em deslocamento na rota.', 'Rastreamento Ao Vivo');

        const interval = setInterval(() => {
            step++;
            const t = step / totalSteps;
            const currentLat = origemLat + (destinoLat - origemLat) * t;
            const currentLng = origemLng + (destinoLng - origemLng) * t;

            markerEntregador.setLatLng([currentLat, currentLng]);
            map.panTo([currentLat, currentLng], { animate: true });

            if (step >= totalSteps) {
                clearInterval(interval);
                simulando = false;
                btnSimular.disabled = false;
                btnSimular.classList.remove('opacity-50');
                statusTxt.innerHTML = '<span class="text-blue-600 font-bold"><i class="fa-solid fa-check"></i> Chegou ao Destino</span>';
                Toast.success('Entregador chegou ao endereço de entrega com sucesso!', 'Entrega Concluída');
            }
        }, 120);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
