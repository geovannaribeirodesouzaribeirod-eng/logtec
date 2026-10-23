<?php
$tituloPagina = 'Cadastrar Conta - LogTec';
require_once __DIR__ . '/includes/header.php';

$tipoAtivo = $_GET['tipo'] ?? 'funcionario';
if (!in_array($tipoAtivo, ['empresa', 'funcionario'])) {
    $tipoAtivo = 'funcionario';
}
?>

<div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full space-y-6">
        
        <!-- Cabeçalho -->
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/25 mb-4">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Crie sua Conta no LogTec
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Cadastre-se como empresa parceira ou entregador profissional
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
            
            <!-- Abas do Tipo de Cadastro -->
            <div class="grid grid-cols-2 border-b border-slate-200 bg-slate-50/70 p-1.5 gap-1.5 text-sm font-semibold">
                <button type="button" id="tab-cad-funcionario" onclick="alternarCadastro('funcionario')" class="py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 <?= $tipoAtivo === 'funcionario' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' ?>">
                    <i class="fa-solid fa-person-biking <?= $tipoAtivo === 'funcionario' ? 'text-emerald-500' : '' ?>"></i>
                    <span>Funcionário (CPF)</span>
                </button>
                <button type="button" id="tab-cad-empresa" onclick="alternarCadastro('empresa')" class="py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 <?= $tipoAtivo === 'empresa' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' ?>">
                    <i class="fa-solid fa-building <?= $tipoAtivo === 'empresa' ? 'text-blue-500' : '' ?>"></i>
                    <span>Empresa (CNPJ)</span>
                </button>
            </div>

            <!-- FORMULÁRIO 1: FUNCIONÁRIO / ENTREGADOR -->
            <form id="form-funcionario" action="actions/auth_action.php" method="POST" class="p-6 sm:p-8 space-y-4 <?= $tipoAtivo === 'empresa' ? 'hidden' : '' ?>">
                <input type="hidden" name="acao" value="cadastro_funcionario">

                <div class="p-3 bg-blue-50/70 border border-blue-200/60 rounded-xl text-xs text-blue-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-blue-600 mt-0.5 text-sm"></i>
                    <div>
                        <strong>Regras Obrigatórias:</strong> Um único cadastro por CPF e idade mínima comprovada de <strong>18 anos completos</strong>.
                    </div>
                </div>

                <!-- Nome Completo -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Completo *</label>
                    <input type="text" name="nome" required placeholder="Ex: Carlos Eduardo Silva" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                </div>

                <!-- E-mail e Telefone em 2 colunas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">E-mail *</label>
                        <input type="email" name="email" required placeholder="carlos@exemplo.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / WhatsApp *</label>
                        <input type="text" name="telefone" data-mask="telefone" required placeholder="(11) 98888-7777" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                </div>

                <!-- CPF e Data de Nascimento (+18 anos) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">CPF (Único no sistema) *</label>
                        <input type="text" name="cpf" data-mask="cpf" required placeholder="000.000.000-00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Data de Nascimento (+18) *</label>
                        <input type="date" name="data_nascimento" id="data_nascimento" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                </div>

                <!-- Veículo e Placa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Veículo de Entrega</label>
                        <select name="veiculo" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm bg-white">
                            <option value="Moto">Motocicleta (Recomendado)</option>
                            <option value="Carro">Carro de Passeio</option>
                            <option value="Van/Fiorino">Utilitário / Fiorino</option>
                            <option value="Bicicleta">Bicicleta Express</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Placa do Veículo</label>
                        <input type="text" name="placa" placeholder="ABC-1D23" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm font-mono uppercase">
                    </div>
                </div>

                <!-- Senha -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Criar Senha de Acesso *</label>
                    <input type="password" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 text-sm mt-4">
                    <i class="fa-solid fa-check"></i>
                    <span>Concluir Cadastro de Funcionário</span>
                </button>
            </form>

            <!-- FORMULÁRIO 2: EMPRESA -->
            <form id="form-empresa" action="actions/auth_action.php" method="POST" class="p-6 sm:p-8 space-y-4 <?= $tipoAtivo === 'funcionario' ? 'hidden' : '' ?>">
                <input type="hidden" name="acao" value="cadastro_empresa">

                <div class="p-3 bg-blue-50/70 border border-blue-200/60 rounded-xl text-xs text-blue-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-building text-blue-600 mt-0.5 text-sm"></i>
                    <div>
                        <strong>Cadastro Empresarial:</strong> Cadastre sua empresa com <strong>CNPJ válido</strong> para despachar entregas em tempo real e contratar entregadores.
                    </div>
                </div>

                <!-- Razão Social e Nome Fantasia -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Razão Social *</label>
                        <input type="text" name="razao_social" required placeholder="Ex: Log Express LTDA" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Fantasia *</label>
                        <input type="text" name="nome_fantasia" required placeholder="Ex: LogTec Hub" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                </div>

                <!-- CNPJ e E-mail -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">CNPJ *</label>
                        <input type="text" name="cnpj" data-mask="cnpj" required placeholder="00.000.000/0000-00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">E-mail Corporativo *</label>
                        <input type="email" name="email" required placeholder="contato@empresa.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                </div>

                <!-- Telefone e Cidade -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / Fixo *</label>
                        <input type="text" name="telefone" data-mask="telefone" required placeholder="(11) 3333-4444" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cidade - UF *</label>
                        <input type="text" name="cidade" required placeholder="São Paulo - SP" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                    </div>
                </div>

                <!-- Endereço -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Endereço da Sede *</label>
                    <input type="text" name="endereco" required placeholder="Av. Paulista, 1000 - Bela Vista" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                </div>

                <!-- Senha -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Criar Senha de Acesso *</label>
                    <input type="password" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-900 text-sm">
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 text-sm mt-4">
                    <i class="fa-solid fa-check"></i>
                    <span>Concluir Cadastro de Empresa</span>
                </button>
            </form>

        </div>

        <p class="text-center text-sm text-slate-500">
            Já tem uma conta cadastrada?
            <a href="login.php" class="font-bold text-blue-600 hover:text-blue-700 underline">
                Entrar agora
            </a>
        </p>

    </div>
</div>

<script>
function alternarCadastro(tipo) {
    const formFunc = document.getElementById('form-funcionario');
    const formEmp = document.getElementById('form-empresa');
    const tabFunc = document.getElementById('tab-cad-funcionario');
    const tabEmp = document.getElementById('tab-cad-empresa');

    if (tipo === 'empresa') {
        formFunc.classList.add('hidden');
        formEmp.classList.remove('hidden');

        tabEmp.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 bg-white text-blue-600 shadow-sm';
        tabFunc.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';
    } else {
        formEmp.classList.add('hidden');
        formFunc.classList.remove('hidden');

        tabFunc.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 bg-white text-blue-600 shadow-sm';
        tabEmp.className = 'py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
