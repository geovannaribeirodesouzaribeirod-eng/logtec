<?php
$tituloPagina = 'Entrar no Sistema - LogTec';
require_once __DIR__ . '/includes/header.php';

$tipoAtivo = $_GET['tipo'] ?? 'funcionario';
if (!in_array($tipoAtivo, ['empresa', 'funcionario'])) {
    $tipoAtivo = 'funcionario';
}
?>

<div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        
        <!-- Cabeçalho do Card -->
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/25 mb-4">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Acesse sua Conta
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-slate-500">
                Selecione o seu perfil de acesso conforme as regras do LogTec
            </p>
        </div>

        <!-- Card de Login -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
            
            <!-- Abas de Alternância: Duas maneiras de entrar -->
            <div class="grid grid-cols-2 border-b border-slate-200 bg-slate-50/70 p-1.5 gap-1.5 text-sm font-semibold">
                <button type="button" id="tab-btn-funcionario" onclick="alternarAba('funcionario')" class="py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 <?= $tipoAtivo === 'funcionario' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' ?>">
                    <i class="fa-solid fa-id-card <?= $tipoAtivo === 'funcionario' ? 'text-emerald-500' : '' ?>"></i>
                    <span>Funcionário (CPF)</span>
                </button>
                <button type="button" id="tab-btn-empresa" onclick="alternarAba('empresa')" class="py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 <?= $tipoAtivo === 'empresa' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' ?>">
                    <i class="fa-solid fa-building <?= $tipoAtivo === 'empresa' ? 'text-blue-500' : '' ?>"></i>
                    <span>Empresa (CNPJ)</span>
                </button>
            </div>

            <!-- Formulário -->
            <form action="actions/auth_action.php" method="POST" class="p-6 sm:p-8 space-y-5">
                <input type="hidden" name="acao" value="login">
                <input type="hidden" name="tipo" id="input-tipo" value="<?= htmlspecialchars($tipoAtivo) ?>">

                <!-- Campo Identificador Dinâmico (CPF ou CNPJ) -->
                <div>
                    <label id="label-identificador" for="identificador" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        <?= $tipoAtivo === 'empresa' ? 'CNPJ da Empresa' : 'CPF do Funcionário' ?>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i id="icon-identificador" class="fa-solid <?= $tipoAtivo === 'empresa' ? 'fa-building' : 'fa-id-card' ?>"></i>
                        </span>
                        <input type="text" name="identificador" id="identificador" required
                            data-mask="<?= $tipoAtivo === 'empresa' ? 'cnpj' : 'cpf' ?>"
                            placeholder="<?= $tipoAtivo === 'empresa' ? '00.000.000/0000-00' : '000.000.000-00' ?>"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm transition">
                    </div>
                </div>

                <!-- Campo Senha -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="senha" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Senha de Acesso
                        </label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="senha" id="senha" required placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm transition">
                    </div>
                </div>

                <!-- Botão de Login -->
                <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Entrar no LogTec</span>
                </button>
            </form>

            <!-- Rodapé do Card: Dica de Credenciais de Demonstração -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-600 space-y-1">
                <p class="font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-blue-500"></i> Contas Pré-cadastradas para Testes:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 font-mono text-[11px]">
                    <div class="p-2 rounded bg-white border border-slate-200">
                        <span class="font-bold text-slate-800">Funcionário:</span><br>
                        CPF: 123.456.789-00<br>
                        Senha: 123456
                    </div>
                    <div class="p-2 rounded bg-white border border-slate-200">
                        <span class="font-bold text-slate-800">Empresa:</span><br>
                        CNPJ: 12.345.678/0001-99<br>
                        Senha: 123456
                    </div>
                </div>
            </div>

        </div>

        <!-- Link para Cadastro -->
        <p class="text-center text-sm text-slate-500">
            Ainda não possui uma conta?
            <a href="cadastro.php" class="font-bold text-blue-600 hover:text-blue-700 underline">
                Cadastre-se gratuitamente
            </a>
        </p>

    </div>
</div>

<script>
function alternarAba(tipo) {
    const inputTipo = document.getElementById('input-tipo');
    const label = document.getElementById('label-identificador');
    const icon = document.getElementById('icon-identificador');
    const input = document.getElementById('identificador');
    const tabFunc = document.getElementById('tab-btn-funcionario');
    const tabEmp = document.getElementById('tab-btn-empresa');

    inputTipo.value = tipo;
    input.value = '';

    if (tipo === 'empresa') {
        label.textContent = 'CNPJ da Empresa';
        input.placeholder = '00.000.000/0000-00';
        input.setAttribute('data-mask', 'cnpj');
        icon.className = 'fa-solid fa-building';
        
        tabEmp.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 bg-white text-blue-600 shadow-sm';
        tabFunc.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';
    } else {
        label.textContent = 'CPF do Funcionário';
        input.placeholder = '000.000.000-00';
        input.setAttribute('data-mask', 'cpf');
        icon.className = 'fa-solid fa-id-card';

        tabFunc.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 bg-white text-blue-600 shadow-sm';
        tabEmp.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
