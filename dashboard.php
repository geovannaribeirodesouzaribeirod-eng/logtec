<?php
$tituloPagina = 'Painel de Controle - LogTec';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/bd.php';

exigirLogin();

$tipo = tipoUsuario();
$usuario = getUsuarioAtual();
$usuarioId = $usuario['id'];

// Carregamento de dados conforme o tipo
if ($tipo === 'empresa') {
    $pedidosPendentes = dbFetchAll("SELECT * FROM pedidos WHERE empresa_id = ? AND status = 'pendente' ORDER BY id DESC", [$usuarioId]);
    $pedidosEmTransito = dbFetchAll("SELECT p.*, u.nome as entregador_nome, u.telefone as entregador_tel, u.veiculo, u.placa 
                                    FROM pedidos p 
                                    LEFT JOIN usuarios u ON p.usuario_id = u.id 
                                    WHERE p.empresa_id = ? AND p.status = 'em_transito' 
                                    ORDER BY p.id DESC", [$usuarioId]);
    $pedidosEntregues = dbFetchAll("SELECT * FROM pedidos WHERE empresa_id = ? AND status = 'entregue' ORDER BY id DESC LIMIT 5", [$usuarioId]);

    $totalPendentes = count($pedidosPendentes);
    $totalEmTransito = count($pedidosEmTransito);
    $totalEntregues = count($pedidosEntregues);
} else {
    // Funcionário
    $pedidosDisponiveis = dbFetchAll("SELECT p.*, e.nome_fantasia as empresa_nome, e.telefone as empresa_tel 
                                      FROM pedidos p 
                                      INNER JOIN empresas e ON p.empresa_id = e.id 
                                      WHERE p.status = 'pendente' 
                                      ORDER BY p.id DESC", []);

    $minhaEntregaAtiva = dbFetchOne("SELECT p.*, e.nome_fantasia as empresa_nome, e.telefone as empresa_tel 
                                     FROM pedidos p 
                                     INNER JOIN empresas e ON p.empresa_id = e.id 
                                     WHERE p.usuario_id = ? AND p.status = 'em_transito' 
                                     LIMIT 1", [$usuarioId]);

    $minhasConcluidas = dbFetchAll("SELECT * FROM pedidos WHERE usuario_id = ? AND status = 'entregue' ORDER BY id DESC", [$usuarioId]);
    $totalGanhos = array_reduce($minhasConcluidas, function($carry, $item) {
        return $carry + floatval($item['valor_frete']);
    }, 0.0);
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Topo do Dashboard -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="pulse-indicator"></span>
                <span class="text-xs uppercase tracking-wider font-bold text-slate-500">
                    <?= $tipo === 'empresa' ? 'Portal Empresarial' : 'Portal do Entregador' ?>
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Olá, <?= htmlspecialchars($usuario['nome_fantasia'] ?? $usuario['nome']) ?>!
            </h1>
            <p class="text-sm text-slate-500">
                <?= $tipo === 'empresa' ? 'CNPJ: ' . htmlspecialchars($usuario['cnpj']) . ' • ' . htmlspecialchars($usuario['cidade']) : 'CPF: ' . htmlspecialchars($usuario['cpf']) . ' • Veículo: ' . htmlspecialchars($usuario['veiculo'] ?? 'Moto') ?>
            </p>
        </div>

        <!-- Ações Rápidas -->
        <div class="flex flex-wrap items-center gap-3">
            <?php if ($tipo === 'empresa'): ?>
                <button type="button" onclick="abrirModalNovoPedido()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Novo Pedido de Entrega</span>
                </button>
            <?php else: ?>
                <a href="rotas.php" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Ver Mapa de Rotas</span>
                </a>
            <?php endif; ?>
            <a href="listas.php" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm transition flex items-center gap-2">
                <i class="fa-solid fa-list-check text-amber-500"></i>
                <span>Minhas Listas</span>
            </a>
        </div>
    </div>

    <?php if ($tipo === 'empresa'): ?>
        <!-- ========================================== -->
        <!-- DASHBOARD DA EMPRESA                       -->
        <!-- ========================================== -->

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pedidos Pendentes</span>
                    <p class="text-3xl font-extrabold text-amber-600 mt-1"><?= $totalPendentes ?></p>
                    <span class="text-xs text-slate-400">Aguardando entregador</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Em Trânsito / Rotas</span>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1"><?= $totalEmTransito ?></p>
                    <span class="text-xs text-slate-400">Entregadores em trajeto</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Entregas Concluídas</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-1"><?= $totalEntregues ?></p>
                    <span class="text-xs text-slate-400">Finalizadas com sucesso</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendentes e Rotas em Andamento -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Pedidos Pendentes -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-amber-500"></i> Pedidos Pendentes de Coleta
                    </h3>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800"><?= $totalPendentes ?></span>
                </div>

                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    <?php if (empty($pedidosPendentes)): ?>
                        <div class="p-8 text-center text-slate-400 text-sm">
                            <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300"></i>
                            <p>Nenhum pedido pendente no momento.</p>
                            <button type="button" onclick="abrirModalNovoPedido()" class="mt-3 text-xs text-blue-600 font-bold hover:underline">
                                + Criar um novo pedido de entrega
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pedidosPendentes as $pedido): ?>
                            <div class="p-4 hover:bg-slate-50 transition">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono font-bold text-blue-600"><?= htmlspecialchars($pedido['codigo_rastreio']) ?></span>
                                            <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold">Pendente</span>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-sm mt-1"><?= htmlspecialchars($pedido['cliente_nome']) ?></h4>
                                        <p class="text-xs text-slate-500"><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> Destino: <?= htmlspecialchars($pedido['destino_endereco']) ?></p>
                                        <p class="text-xs text-slate-600 mt-1"><strong>Item:</strong> <?= htmlspecialchars($pedido['descricao_itens']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-base font-extrabold text-emerald-600">R$ <?= number_format($pedido['valor_frete'], 2, ',', '.') ?></p>
                                        <span class="text-[11px] text-slate-400"><?= htmlspecialchars($pedido['prazo_estimado']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Entregas em Trânsito -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-motorcycle text-blue-600"></i> Entregas em Trânsito em Tempo Real
                    </h3>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800"><?= $totalEmTransito ?></span>
                </div>

                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    <?php if (empty($pedidosEmTransito)): ?>
                        <div class="p-8 text-center text-slate-400 text-sm">
                            <i class="fa-solid fa-route text-3xl mb-2 text-slate-300"></i>
                            <p>Nenhuma entrega em rota ativa no momento.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pedidosEmTransito as $transito): ?>
                            <div class="p-4 hover:bg-slate-50 transition">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono font-bold text-blue-600"><?= htmlspecialchars($transito['codigo_rastreio']) ?></span>
                                            <span class="text-[10px] bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-bold">Em Rota</span>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-sm mt-1"><?= htmlspecialchars($transito['cliente_nome']) ?></h4>
                                        <p class="text-xs text-slate-600 mt-1">
                                            <i class="fa-solid fa-helmet-safety text-emerald-600 mr-1"></i>
                                            Entregador: <strong><?= htmlspecialchars($transito['entregador_nome'] ?? 'Parceiro') ?></strong> (<?= htmlspecialchars($transito['veiculo'] ?? 'Moto') ?>)
                                        </p>
                                        <p class="text-xs text-slate-500"><i class="fa-solid fa-map-pin text-rose-500 mr-1"></i> <?= htmlspecialchars($transito['destino_endereco']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <a href="rotas.php?pedido=<?= $transito['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition">
                                            <i class="fa-solid fa-map-location-dot"></i> Rastrear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    <?php else: ?>
        <!-- ========================================== -->
        <!-- DASHBOARD DO FUNCIONÁRIO / ENTREGADOR      -->
        <!-- ========================================== -->

        <!-- Minha Entrega Ativa em Destaque (Se houver) -->
        <?php if ($minhaEntregaAtiva): ?>
            <div class="bg-gradient-to-r from-blue-900 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-blue-800/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-blue-800/60 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="pulse-indicator"></span>
                        <h3 class="font-bold text-lg text-white">Sua Rota Ativa no Momento</h3>
                        <span class="text-xs font-mono bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded border border-blue-400/20">
                            <?= htmlspecialchars($minhaEntregaAtiva['codigo_rastreio']) ?>
                        </span>
                    </div>
                    <a href="rotas.php?pedido=<?= $minhaEntregaAtiva['id'] ?>" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 self-start sm:self-auto">
                        <i class="fa-solid fa-location-arrow"></i>
                        <span>Abrir Navegação e Mapa</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div>
                        <span class="text-xs text-slate-400">Origem / Empresa</span>
                        <p class="font-bold text-white text-base"><?= htmlspecialchars($minhaEntregaAtiva['empresa_nome']) ?></p>
                        <p class="text-xs text-slate-300"><?= htmlspecialchars($minhaEntregaAtiva['origem_endereco']) ?></p>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400">Destino do Cliente</span>
                        <p class="font-bold text-white text-base"><?= htmlspecialchars($minhaEntregaAtiva['cliente_nome']) ?></p>
                        <p class="text-xs text-slate-300"><?= htmlspecialchars($minhaEntregaAtiva['destino_endereco']) ?></p>
                    </div>

                    <div class="bg-blue-950/60 p-3.5 rounded-xl border border-blue-800/40 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400">Valor a Receber</span>
                            <p class="text-2xl font-extrabold text-emerald-400">R$ <?= number_format($minhaEntregaAtiva['valor_frete'], 2, ',', '.') ?></p>
                        </div>
                        <form action="actions/pedidos_action.php" method="POST">
                            <input type="hidden" name="acao" value="atualizar_status">
                            <input type="hidden" name="pedido_id" value="<?= $minhaEntregaAtiva['id'] ?>">
                            <input type="hidden" name="status" value="entregue">
                            <button type="submit" data-confirm="Confirmar que o pacote foi entregue ao cliente?" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow transition">
                                Finalizar Entrega
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cards de Estatísticas do Entregador -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Disponíveis p/ Aceitar</span>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1"><?= count($pedidosDisponiveis) ?></p>
                    <span class="text-xs text-slate-400">Novas oportunidades</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Entregas Concluídas</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-1"><?= count($minhasConcluidas) ?></p>
                    <span class="text-xs text-slate-400">No seu histórico</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total de Ganhos</span>
                    <p class="text-3xl font-extrabold text-slate-900 mt-1">R$ <?= number_format($totalGanhos, 2, ',', '.') ?></p>
                    <span class="text-xs text-emerald-600 font-semibold">Saldo acumulado</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendentes Disponíveis para Aceitar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Pedidos Pendentes Prontos para Entrega
                    </h3>
                    <p class="text-xs text-slate-500">Aceite rotas e ganhe por cada pacote entregue</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-mono">
                    <?= count($pedidosDisponiveis) ?> abertos
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                <?php if (empty($pedidosDisponiveis)): ?>
                    <div class="p-8 text-center text-slate-400 text-sm">
                        <i class="fa-solid fa-clipboard-check text-3xl mb-2 text-slate-300"></i>
                        <p>No momento não há pedidos pendentes na sua região. Volte em breve!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pedidosDisponiveis as $pedido): ?>
                        <div class="p-5 hover:bg-slate-50/80 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded"><?= htmlspecialchars($pedido['codigo_rastreio']) ?></span>
                                    <span class="text-xs font-bold text-slate-700"><?= htmlspecialchars($pedido['empresa_nome']) ?></span>
                                </div>
                                <div class="text-xs text-slate-600 space-y-0.5">
                                    <p><i class="fa-solid fa-circle-dot text-emerald-500 mr-1.5"></i> <strong>Origem:</strong> <?= htmlspecialchars($pedido['origem_endereco']) ?></p>
                                    <p><i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> <strong>Destino:</strong> <?= htmlspecialchars($pedido['destino_endereco']) ?></p>
                                    <p><i class="fa-solid fa-box text-slate-400 mr-1.5"></i> <strong>Pacote:</strong> <?= htmlspecialchars($pedido['descricao_itens']) ?></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between md:flex-col md:items-end gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                                <div class="text-left md:text-right">
                                    <span class="text-xs text-slate-400">Você recebe:</span>
                                    <p class="text-xl font-extrabold text-emerald-600">R$ <?= number_format($pedido['valor_frete'], 2, ',', '.') ?></p>
                                    <span class="text-[11px] text-slate-500 font-medium"><?= htmlspecialchars($pedido['prazo_estimado']) ?></span>
                                </div>

                                <form action="actions/pedidos_action.php" method="POST">
                                    <input type="hidden" name="acao" value="aceitar_pedido">
                                    <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                    <button type="submit" data-confirm="Deseja aceitar este pedido e iniciar a rota agora?" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-motorcycle"></i>
                                        <span>Aceitar Entrega</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

</div>

<!-- Modal para Criação de Pedido de Entrega (Empresa) -->
<div id="modal-novo-pedido-container" class="hidden">
    <div id="modal-novo-pedido-content">
        <form action="actions/pedidos_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="criar_pedido">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nome do Cliente *</label>
                    <input type="text" name="cliente_nome" required placeholder="Ex: Ana Clara Lima" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Telefone do Cliente *</label>
                    <input type="text" name="cliente_telefone" data-mask="telefone" required placeholder="(11) 99999-8888" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Endereço de Origem (Coleta) *</label>
                <input type="text" name="origem_endereco" value="<?= htmlspecialchars($usuario['endereco'] ?? 'Av. Paulista, 1000') ?>" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Endereço de Destino (Entrega) *</label>
                <input type="text" name="destino_endereco" required placeholder="Rua, Número, Bairro e Cidade" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Descrição do Pacote / Itens *</label>
                <textarea name="descricao_itens" required rows="2" placeholder="Ex: 1x Notebook Dell + Carregador (Carga Frágil)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Valor do Frete (R$) *</label>
                    <input type="number" step="0.50" min="5" name="valor_frete" value="22.50" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Prazo Estimado</label>
                    <input type="text" name="prazo_estimado" value="35 - 45 min" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="AppModal.close()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20">
                    Cadastrar e Despachar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalNovoPedido() {
    const html = document.getElementById('modal-novo-pedido-content').innerHTML;
    AppModal.open({
        title: 'Criar Novo Pedido de Entrega',
        body: html,
        footer: '',
        icon: '<i class="fa-solid fa-box text-blue-600"></i>'
    });
    // Reinicia máscaras dentro do modal
    InputMasks.init();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
