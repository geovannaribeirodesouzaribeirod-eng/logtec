<?php
$tituloPagina = 'Templates de Listas - LogTec';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/bd.php';

// Se não estiver logado, redireciona para login
if (!usuarioLogado()) {
    definirToast('info', 'Faça login para criar e gerenciar seus templates de listas.', 'Acesso Necessário');
    header('Location: login.php');
    exit;
}

$tipo = tipoUsuario();
$usuario = getUsuarioAtual();
$autorId = $usuario['id'];

// Obtém todas as listas do usuário
$listas = dbFetchAll("SELECT * FROM listas WHERE tipo_autor = ? AND autor_id = ? ORDER BY id DESC", [$tipo, $autorId]);

// Se não tiver listas ainda, gera templates padrão
if (empty($listas)) {
    $templatesPadrao = [
        ['titulo' => 'Lista de Mercado Semanal', 'cat' => 'mercado', 'icone' => 'fa-cart-shopping', 'cor' => 'emerald'],
        ['titulo' => 'Músicas para Minhas Rotas', 'cat' => 'musica', 'icone' => 'fa-music', 'cor' => 'violet'],
        ['titulo' => 'Filmes para o Fim de Semana', 'cat' => 'filmes', 'icone' => 'fa-film', 'cor' => 'amber'],
        ['titulo' => 'Planejamento de Viagem', 'cat' => 'viagem', 'icone' => 'fa-plane-departure', 'cor' => 'sky']
    ];

    foreach ($templatesPadrao as $tpl) {
        dbExecute("INSERT INTO listas (tipo_autor, autor_id, titulo, categoria, icone, cor) VALUES (?, ?, ?, ?, ?, ?)", [
            $tipo, $autorId, $tpl['titulo'], $tpl['cat'], $tpl['icone'], $tpl['cor']
        ]);
        $novoId = dbLastInsertId();
        dbExecute("INSERT INTO itens_lista (lista_id, descricao, concluido) VALUES (?, ?, 1)", [$novoId, 'Item de exemplo concluído']);
        dbExecute("INSERT INTO itens_lista (lista_id, descricao, concluido) VALUES (?, ?, 0)", [$novoId, 'Novo item a realizar']);
    }
    $listas = dbFetchAll("SELECT * FROM listas WHERE tipo_autor = ? AND autor_id = ? ORDER BY id DESC", [$tipo, $autorId]);
}

// Lista selecionada atualmente
$listaIdAtiva = intval($_GET['lista_id'] ?? 0);
$listaAtiva = null;

if ($listaIdAtiva > 0) {
    foreach ($listas as $l) {
        if ($l['id'] == $listaIdAtiva) {
            $listaAtiva = $l;
            break;
        }
    }
}

if (!$listaAtiva && !empty($listas)) {
    $listaAtiva = $listas[0];
}

$itensLista = [];
if ($listaAtiva) {
    $itensLista = dbFetchAll("SELECT * FROM itens_lista WHERE lista_id = ? ORDER BY id ASC", [$listaAtiva['id']]);
}

$totalItens = count($itensLista);
$concluidos = count(array_filter($itensLista, fn($i) => $i['concluido'] == 1));
$percentual = $totalItens > 0 ? round(($concluidos / $totalItens) * 100) : 0;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200/60 mb-1">
                <i class="fa-solid fa-shapes"></i>
                <span>Recurso do Requisitos.md</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Templates de Listas Personalizáveis
            </h1>
            <p class="text-sm text-slate-500">
                Organize listas de mercado, músicas, filmes, viagens ou entregas com ícones customizáveis.
            </p>
        </div>

        <button type="button" onclick="abrirModalNovaLista()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>Criar Nova Lista</span>
        </button>
    </div>

    <!-- Layout Grid: Listas Disponíveis (Sidebar) + Conteúdo da Lista Selecionada -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Painel Lateral: Todas as Listas do Usuário (4 Colunas) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center justify-between mb-3 px-2">
                    <h3 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Suas Listas</h3>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold"><?= count($listas) ?></span>
                </div>

                <div class="space-y-2">
                    <?php foreach ($listas as $l): ?>
                        <?php $ativa = ($listaAtiva && $listaAtiva['id'] == $l['id']); ?>
                        <a href="listas.php?lista_id=<?= $l['id'] ?>" class="flex items-center justify-between p-3 rounded-xl border transition <?= $ativa ? 'bg-blue-50/80 border-blue-400 text-blue-900 shadow-sm' : 'border-slate-100 hover:bg-slate-50 text-slate-700' ?>">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-white border border-slate-200 shadow-xs">
                                    <i class="fa-solid <?= htmlspecialchars($l['icone']) ?> text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm leading-tight"><?= htmlspecialchars($l['titulo']) ?></p>
                                    <span class="text-[11px] text-slate-400 capitalize"><?= htmlspecialchars($l['categoria']) ?></span>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs <?= $ativa ? 'text-blue-600' : 'text-slate-300' ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Templates Rápidos Pré-configurados -->
            <div class="p-4 bg-slate-100/70 rounded-2xl border border-slate-200 text-xs space-y-3">
                <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Templates Rápidos Sugeridos</h4>
                <div class="grid grid-cols-2 gap-2">
                    <form action="actions/listas_action.php" method="POST">
                        <input type="hidden" name="acao" value="criar_lista">
                        <input type="hidden" name="titulo" value="Lista de Mercado">
                        <input type="hidden" name="categoria" value="mercado">
                        <input type="hidden" name="icone" value="fa-cart-shopping">
                        <input type="hidden" name="cor" value="emerald">
                        <button type="submit" class="w-full text-left p-2.5 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 transition flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping text-emerald-600"></i>
                            <span class="font-medium text-slate-800">Mercado</span>
                        </button>
                    </form>

                    <form action="actions/listas_action.php" method="POST">
                        <input type="hidden" name="acao" value="criar_lista">
                        <input type="hidden" name="titulo" value="Minhas Músicas">
                        <input type="hidden" name="categoria" value="musica">
                        <input type="hidden" name="icone" value="fa-music">
                        <input type="hidden" name="cor" value="violet">
                        <button type="submit" class="w-full text-left p-2.5 rounded-xl bg-white border border-slate-200 hover:border-violet-400 transition flex items-center gap-2">
                            <i class="fa-solid fa-music text-violet-600"></i>
                            <span class="font-medium text-slate-800">Músicas</span>
                        </button>
                    </form>

                    <form action="actions/listas_action.php" method="POST">
                        <input type="hidden" name="acao" value="criar_lista">
                        <input type="hidden" name="titulo" value="Lista de Filmes">
                        <input type="hidden" name="categoria" value="filmes">
                        <input type="hidden" name="icone" value="fa-film">
                        <input type="hidden" name="cor" value="amber">
                        <button type="submit" class="w-full text-left p-2.5 rounded-xl bg-white border border-slate-200 hover:border-amber-400 transition flex items-center gap-2">
                            <i class="fa-solid fa-film text-amber-600"></i>
                            <span class="font-medium text-slate-800">Filmes</span>
                        </button>
                    </form>

                    <form action="actions/listas_action.php" method="POST">
                        <input type="hidden" name="acao" value="criar_lista">
                        <input type="hidden" name="titulo" value="Minha Viagem">
                        <input type="hidden" name="categoria" value="viagem">
                        <input type="hidden" name="icone" value="fa-plane-departure">
                        <input type="hidden" name="cor" value="sky">
                        <button type="submit" class="w-full text-left p-2.5 rounded-xl bg-white border border-slate-200 hover:border-sky-400 transition flex items-center gap-2">
                            <i class="fa-solid fa-plane-departure text-sky-600"></i>
                            <span class="font-medium text-slate-800">Viagens</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Área Principal: Detalhes e Itens da Lista Selecionada (8 Colunas) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <?php if ($listaAtiva): ?>
                
                <!-- Cabeçalho da Lista Ativa com Botão para MUDAR ÍCONE -->
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 shadow-md flex items-center justify-center text-2xl text-blue-600">
                            <i class="fa-solid <?= htmlspecialchars($listaAtiva['icone']) ?>"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl font-extrabold text-slate-900"><?= htmlspecialchars($listaAtiva['titulo']) ?></h2>
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 capitalize">
                                    <?= htmlspecialchars($listaAtiva['categoria']) ?>
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">Criada em <?= date('d/m/Y', strtotime($listaAtiva['criado_em'])) ?></p>
                        </div>
                    </div>

                    <!-- Botão: Alterar Ícone e Ações -->
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="abrirModalAlterarIcone(<?= $listaAtiva['id'] ?>, '<?= htmlspecialchars($listaAtiva['titulo']) ?>', '<?= htmlspecialchars($listaAtiva['icone']) ?>')" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-icons text-blue-600"></i>
                            <span>Alterar Ícone</span>
                        </button>

                        <a href="actions/listas_action.php?acao=excluir_lista&lista_id=<?= $listaAtiva['id'] ?>" data-confirm="Deseja realmente excluir esta lista e todos os seus itens?" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Excluir Lista">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Barra de Progresso de Conclusão -->
                <div class="px-6 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-700">Progresso da Lista:</span>
                        <span class="text-slate-500"><?= $concluidos ?> de <?= $totalItens ?> concluídos (<?= $percentual ?>%)</span>
                    </div>
                    <div class="w-32 bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: <?= $percentual ?>%"></div>
                    </div>
                </div>

                <!-- Formulário para Adicionar Item -->
                <form action="actions/listas_action.php" method="POST" class="p-6 border-b border-slate-100 flex gap-2">
                    <input type="hidden" name="acao" value="adicionar_item">
                    <input type="hidden" name="lista_id" value="<?= $listaAtiva['id'] ?>">
                    
                    <input type="text" name="descricao" required placeholder="Digite um novo item para esta lista..." class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-800 text-sm">
                    
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i>
                        <span>Adicionar</span>
                    </button>
                </form>

                <!-- Listagem de Itens com Checkbox e Ações -->
                <div class="divide-y divide-slate-100">
                    <?php if (empty($itensLista)): ?>
                        <div class="p-10 text-center text-slate-400 text-sm">
                            <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-300"></i>
                            <p>Sua lista está vazia. Adicione o primeiro item acima!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($itensLista as $item): ?>
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition">
                                <a href="actions/listas_action.php?acao=toggle_item&item_id=<?= $item['id'] ?>&lista_id=<?= $listaAtiva['id'] ?>" class="flex items-center gap-3 flex-1 group">
                                    <div class="w-5 h-5 rounded-md border flex items-center justify-center transition <?= $item['concluido'] ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 bg-white group-hover:border-blue-500' ?>">
                                        <?php if ($item['concluido']): ?>
                                            <i class="fa-solid fa-check text-xs"></i>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-sm font-medium <?= $item['concluido'] ? 'line-through text-slate-400' : 'text-slate-800' ?>">
                                        <?= htmlspecialchars($item['descricao']) ?>
                                    </span>
                                </a>

                                <a href="actions/listas_action.php?acao=excluir_item&item_id=<?= $item['id'] ?>&lista_id=<?= $listaAtiva['id'] ?>" data-confirm="Excluir este item?" class="text-slate-300 hover:text-rose-500 p-1.5 transition">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>

    </div>

</div>

<!-- Modal: Criar Nova Lista -->
<div id="modal-nova-lista-container" class="hidden">
    <div id="modal-nova-lista-content">
        <form action="actions/listas_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="criar_lista">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Título da Lista *</label>
                <input type="text" name="titulo" required placeholder="Ex: Compras da Semana, Filmes 2026..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Template / Categoria</label>
                <select name="categoria" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                    <option value="mercado">Lista de Mercado 🛒</option>
                    <option value="musica">Lista de Músicas 🎵</option>
                    <option value="filmes">Lista de Filmes 🎬</option>
                    <option value="viagem">Lista de Viagens ✈️</option>
                    <option value="entregas">Lista Operacional / Entregas 📦</option>
                    <option value="geral">Outra / Geral 📋</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Escolha o Ícone Inicial</label>
                <div class="grid grid-cols-6 gap-2 text-center text-lg">
                    <?php 
                    $iconesDisponiveis = [
                        'fa-cart-shopping', 'fa-music', 'fa-film', 'fa-plane-departure', 
                        'fa-box', 'fa-motorcycle', 'fa-basket-shopping', 'fa-headphones', 
                        'fa-clapperboard', 'fa-car', 'fa-heart', 'fa-star'
                    ];
                    foreach ($iconesDisponiveis as $idx => $ico): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="icone" value="<?= $ico ?>" <?= $idx === 0 ? 'checked' : '' ?> class="sr-only peer">
                            <div class="p-2.5 rounded-xl border border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 text-slate-600 peer-checked:text-blue-600 transition hover:bg-slate-50">
                                <i class="fa-solid <?= $ico ?>"></i>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="AppModal.close()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20">Criar Lista</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Alterar Ícone da Lista -->
<div id="modal-alterar-icone-container" class="hidden">
    <div id="modal-alterar-icone-content">
        <form action="actions/listas_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="alterar_icone">
            <input type="hidden" name="lista_id" id="edit-lista-id" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Título da Lista</label>
                <input type="text" name="titulo" id="edit-lista-titulo" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Selecione o Novo Ícone</label>
                <div class="grid grid-cols-6 gap-2 text-center text-lg max-h-48 overflow-y-auto p-1">
                    <?php 
                    $todosIcones = [
                        'fa-cart-shopping', 'fa-music', 'fa-film', 'fa-plane-departure', 
                        'fa-box', 'fa-motorcycle', 'fa-basket-shopping', 'fa-headphones', 
                        'fa-clapperboard', 'fa-car', 'fa-heart', 'fa-star', 'fa-mug-hot',
                        'fa-book', 'fa-tv', 'fa-dumbbell', 'fa-gamepad', 'fa-pizza-slice',
                        'fa-camera', 'fa-bicycle', 'fa-suitcase-rolling', 'fa-list-check',
                        'fa-bell', 'fa-tag'
                    ];
                    foreach ($todosIcones as $ico): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="icone" value="<?= $ico ?>" class="sr-only peer edit-icone-radio">
                            <div class="p-2.5 rounded-xl border border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 text-slate-600 peer-checked:text-blue-600 transition hover:bg-slate-50">
                                <i class="fa-solid <?= $ico ?>"></i>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="AppModal.close()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalNovaLista() {
    const html = document.getElementById('modal-nova-lista-content').innerHTML;
    AppModal.open({
        title: 'Criar Novo Template de Lista',
        body: html,
        footer: '',
        icon: '<i class="fa-solid fa-list-check text-blue-600"></i>'
    });
}

function abrirModalAlterarIcone(listaId, titulo, iconeAtual) {
    const container = document.getElementById('modal-alterar-icone-content');
    container.querySelector('#edit-lista-id').value = listaId;
    container.querySelector('#edit-lista-titulo').value = titulo;

    // Marca o ícone atual
    const radios = container.querySelectorAll('.edit-icone-radio');
    radios.forEach(r => {
        if (r.value === iconeAtual) r.checked = true;
    });

    AppModal.open({
        title: 'Personalizar Ícone da Lista',
        body: container.innerHTML,
        footer: '',
        icon: '<i class="fa-solid fa-icons text-blue-600"></i>'
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
