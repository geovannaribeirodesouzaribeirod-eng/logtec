<?php
$tituloPagina = 'Vagas de Emprego e Oportunidades - LogTec';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/bd.php';

$tipo = tipoUsuario();
$usuario = getUsuarioAtual();

// Lista todas as vagas abertas
$vagas = dbFetchAll("SELECT v.*, e.nome_fantasia as empresa_nome, e.cidade as empresa_cidade, e.telefone as empresa_tel 
                     FROM vagas v 
                     INNER JOIN empresas e ON v.empresa_id = e.id 
                     WHERE v.status = 'aberta' 
                     ORDER BY v.id DESC");

// Se for funcionário, carrega suas candidaturas
$minhasCandidaturas = [];
if ($tipo === 'funcionario' && $usuario) {
    $minhasCandidaturas = dbFetchAll("SELECT c.*, v.titulo as vaga_titulo, e.nome_fantasia as empresa_nome 
                                     FROM candidaturas c 
                                     INNER JOIN vagas v ON c.vaga_id = v.id 
                                     INNER JOIN empresas e ON v.empresa_id = e.id 
                                     WHERE c.usuario_id = ? 
                                     ORDER BY c.id DESC", [$usuario['id']]);
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 text-purple-800 text-xs font-bold border border-purple-200/60 mb-1">
                <i class="fa-solid fa-briefcase"></i>
                <span>Funcionários à Procura de Emprego</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Vagas de Emprego e Oportunidades
            </h1>
            <p class="text-sm text-slate-500">
                Conectando empresas com demandas logísticas a profissionais habilitados para entregas.
            </p>
        </div>

        <?php if ($tipo === 'empresa'): ?>
            <button type="button" onclick="abrirModalNovaVaga()" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-md shadow-purple-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Publicar Nova Vaga</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Se for funcionário e tiver candidaturas -->
    <?php if ($tipo === 'funcionario' && !empty($minhasCandidaturas)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-purple-600"></i> Suas Candidaturas Ativas
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <?php foreach ($minhasCandidaturas as $c): ?>
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($c['vaga_titulo']) ?></h4>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($c['empresa_nome']) ?></p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $c['status'] === 'aceita' ? 'bg-emerald-100 text-emerald-800' : ($c['status'] === 'recusada' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') ?>">
                            <?= ucfirst($c['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista de Vagas Abertas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($vagas)): ?>
            <div class="col-span-full p-12 bg-white rounded-2xl border border-slate-200 text-center text-slate-400">
                <i class="fa-solid fa-briefcase text-4xl mb-3 text-slate-300"></i>
                <p class="text-base font-semibold text-slate-600">Nenhuma vaga aberta no momento.</p>
                <p class="text-xs text-slate-400 mt-1">Fique atento, novas empresas cadastram oportunidades diariamente.</p>
            </div>
        <?php else: ?>
            <?php foreach ($vagas as $vaga): ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between p-6">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600 bg-purple-50 px-2 py-0.5 rounded-md">
                                    <?= htmlspecialchars($vaga['tipo_contrato']) ?>
                                </span>
                                <h3 class="text-lg font-bold text-slate-900 mt-1.5 leading-snug">
                                    <?= htmlspecialchars($vaga['titulo']) ?>
                                </h3>
                            </div>
                        </div>

                        <p class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-building text-slate-400"></i>
                            <?= htmlspecialchars($vaga['empresa_nome']) ?> • <?= htmlspecialchars($vaga['empresa_cidade']) ?>
                        </p>

                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                            <?= htmlspecialchars($vaga['descricao']) ?>
                        </p>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600">
                            <strong>Requisitos:</strong> <?= htmlspecialchars($vaga['requisitos']) ?>
                        </div>
                    </div>

                    <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Remuneração</span>
                            <p class="text-base font-extrabold text-emerald-600"><?= htmlspecialchars($vaga['remuneracao']) ?></p>
                        </div>

                        <?php if ($tipo === 'funcionario'): ?>
                            <button type="button" onclick="abrirModalCandidatura(<?= $vaga['id'] ?>, '<?= htmlspecialchars($vaga['titulo']) ?>')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                Candidatar-se
                            </button>
                        <?php elseif (!usuarioLogado()): ?>
                            <a href="login.php?tipo=funcionario" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition">
                                Entrar p/ Candidatar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- Modal: Publicar Nova Vaga (Empresas) -->
<div id="modal-nova-vaga-container" class="hidden">
    <div id="modal-nova-vaga-content">
        <form action="actions/vagas_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="criar_vaga">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Título do Cargo / Vaga *</label>
                <input type="text" name="titulo" required placeholder="Ex: Entregador Motociclista Noturno" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Modelo de Contratação</label>
                    <select name="tipo_contrato" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm bg-white">
                        <option value="Entregador Parceiro (MEI/Diárias)">Entregador Parceiro (MEI)</option>
                        <option value="CLT Efetivo">CLT Efetivo</option>
                        <option value="Prestador Autônomo">Prestador Autônomo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Estimativa de Ganho *</label>
                    <input type="text" name="remuneracao" required value="R$ 180 a R$ 260 / dia" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Descrição das Atividades *</label>
                <textarea name="descricao" rows="3" required placeholder="Descreva os turnos, rotas atendidas e rotina de entrega..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Requisitos Mínimos</label>
                <input type="text" name="requisitos" value="CNH A/B válida, smartphone com internet e maioridade" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="AppModal.close()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-bold shadow-md shadow-purple-500/20">Publicar Vaga</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Candidatura do Funcionário -->
<div id="modal-candidatura-container" class="hidden">
    <div id="modal-candidatura-content">
        <form action="actions/vagas_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="acao" value="candidatar">
            <input type="hidden" name="vaga_id" id="cand-vaga-id" value="">

            <p class="text-xs text-slate-600">
                Seus dados de perfil (Nome, Telefone, CPF e Veículo) serão enviados automaticamente para o RH da empresa contratante.
            </p>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mensagem de Apresentação (Opcional)</label>
                <textarea name="mensagem" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">Olá! Tenho total disponibilidade para a função e possuo veículo próprio em ótimo estado para entregas pontuais.</textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="AppModal.close()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-bold shadow-md shadow-purple-500/20">Enviar Candidatura</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalNovaVaga() {
    const html = document.getElementById('modal-nova-vaga-content').innerHTML;
    AppModal.open({
        title: 'Publicar Nova Oportunidade',
        body: html,
        footer: '',
        icon: '<i class="fa-solid fa-briefcase text-purple-600"></i>'
    });
}

function abrirModalCandidatura(vagaId, tituloVaga) {
    const container = document.getElementById('modal-candidatura-content');
    container.querySelector('#cand-vaga-id').value = vagaId;

    AppModal.open({
        title: 'Candidatar-se: ' + tituloVaga,
        body: container.innerHTML,
        footer: '',
        icon: '<i class="fa-solid fa-paper-plane text-purple-600"></i>'
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
