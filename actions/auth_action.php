<?php
/**
 * LogTec - Processador de Autenticação e Cadastro
 *
 * Regras Atendidas:
 * - Sessões nativas do PHP
 * - Cadastro com CPF único por usuário
 * - Validação de Maioridade (+18 anos)
 * - Duas maneiras de entrar: Empresa (CNPJ) e Funcionário (CPF)
 * - Mensagens claras via TOAST
 */

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../bd.php';

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

if ($acao === 'login') {
    $tipo = $_POST['tipo'] ?? 'funcionario';
    $identificador = trim($_POST['identificador'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($identificador) || empty($senha)) {
        definirToast('error', 'Por favor, preencha todos os campos para entrar.', 'Campos Obrigatórios');
        header('Location: ../login.php?tipo=' . urlencode($tipo));
        exit;
    }

    try {
        if ($tipo === 'empresa') {
            // Login por CNPJ para Empresas
            $cnpjLimpo = preg_replace('/[^0-9]/', '', $identificador);
            $sql = "SELECT * FROM empresas WHERE REPLACE(REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', ''), ' ', '') = ? LIMIT 1";
            $empresa = dbFetchOne($sql, [$cnpjLimpo]);

            if ($empresa && password_verify($senha, $empresa['senha'])) {
                $_SESSION['usuario_logado'] = true;
                $_SESSION['usuario_tipo'] = 'empresa';
                $_SESSION['usuario_dados'] = $empresa;

                definirToast('success', 'Bem-vindo de volta, ' . $empresa['nome_fantasia'] . '!', 'Login Realizado');
                header('Location: ../dashboard.php');
                exit;
            } else {
                definirToast('error', 'CNPJ ou senha incorretos.', 'Falha de Autenticação');
                header('Location: ../login.php?tipo=empresa');
                exit;
            }
        } else {
            // Login por CPF para Funcionários
            $cpfLimpo = preg_replace('/[^0-9]/', '', $identificador);
            $sql = "SELECT * FROM usuarios WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ? LIMIT 1";
            $usuario = dbFetchOne($sql, [$cpfLimpo]);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_logado'] = true;
                $_SESSION['usuario_tipo'] = 'funcionario';
                $_SESSION['usuario_dados'] = $usuario;

                definirToast('success', 'Olá, ' . $usuario['nome'] . '! Bom trabalho hoje.', 'Login Realizado');
                header('Location: ../dashboard.php');
                exit;
            } else {
                definirToast('error', 'CPF ou senha incorretos.', 'Falha de Autenticação');
                header('Location: ../login.php?tipo=funcionario');
                exit;
            }
        }
    } catch (Exception $e) {
        definirToast('error', 'Erro ao processar login: ' . $e->getMessage(), 'Erro no Sistema');
        header('Location: ../login.php');
        exit;
    }
}

elseif ($acao === 'cadastro_funcionario') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $dataNascimento = trim($_POST['data_nascimento'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $veiculo = trim($_POST['veiculo'] ?? 'Moto');
    $placa = trim($_POST['placa'] ?? '');

    // 1. Validação de preenchimento
    if (empty($nome) || empty($email) || empty($cpf) || empty($dataNascimento) || empty($senha)) {
        definirToast('error', 'Preencha todos os campos obrigatórios do cadastro.', 'Dados Incompletos');
        header('Location: ../cadastro.php?tipo=funcionario');
        exit;
    }

    // 2. Validação de Maioridade (+18 anos) - Regra do CORE
    if (!validarMaioridade($dataNascimento)) {
        definirToast('error', 'Cadastro permitido apenas para maiores de 18 anos.', 'Idade Mínima Requerida');
        header('Location: ../cadastro.php?tipo=funcionario');
        exit;
    }

    // 3. Validação matemática do CPF
    if (!validarCPF($cpf)) {
        definirToast('error', 'O CPF informado é inválido. Verifique os dígitos e tente novamente.', 'CPF Inválido');
        header('Location: ../cadastro.php?tipo=funcionario');
        exit;
    }

    $cpfLimpo = preg_replace('/[^0-9]/', '', $cpf);
    $cpfFormatado = formatarCPF($cpfLimpo);

    try {
        // 4. Regra do CORE: "um único usuário por cpf"
        $existeCpf = dbFetchOne("SELECT id FROM usuarios WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ? LIMIT 1", [$cpfLimpo]);
        if ($existeCpf) {
            definirToast('warning', 'Este CPF já está cadastrado em nosso sistema. Faça o login.', 'CPF Já Cadastrado');
            header('Location: ../login.php?tipo=funcionario');
            exit;
        }

        // Verifica unicidade de e-mail
        $existeEmail = dbFetchOne("SELECT id FROM usuarios WHERE email = ? LIMIT 1", [$email]);
        if ($existeEmail) {
            definirToast('warning', 'Este e-mail já está em uso por outro usuário.', 'E-mail Duplicado');
            header('Location: ../cadastro.php?tipo=funcionario');
            exit;
        }

        // Cadastro seguro com hash bcrypt
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, telefone, senha, veiculo, placa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        dbExecute($sql, [$nome, $email, $cpfFormatado, $dataNascimento, $telefone, $senhaHash, $veiculo, $placa]);
        $novoId = dbLastInsertId();

        // Autentica o usuário imediatamente na sessão
        $usuarioCriado = dbFetchOne("SELECT * FROM usuarios WHERE id = ?", [$novoId]);
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_tipo'] = 'funcionario';
        $_SESSION['usuario_dados'] = $usuarioCriado;

        definirToast('success', 'Cadastro realizado com sucesso! Bem-vindo ao LogTec.', 'Conta Criada');
        header('Location: ../dashboard.php');
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao salvar cadastro: ' . $e->getMessage(), 'Erro no Banco');
        header('Location: ../cadastro.php?tipo=funcionario');
        exit;
    }
}

elseif ($acao === 'cadastro_empresa') {
    $razaoSocial = trim($_POST['razao_social'] ?? '');
    $nomeFantasia = trim($_POST['nome_fantasia'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($razaoSocial) || empty($nomeFantasia) || empty($cnpj) || empty($email) || empty($senha)) {
        definirToast('error', 'Preencha os campos obrigatórios da empresa.', 'Dados Incompletos');
        header('Location: ../cadastro.php?tipo=empresa');
        exit;
    }

    if (!validarCNPJ($cnpj)) {
        definirToast('error', 'O CNPJ informado é inválido. Por favor, confira os números.', 'CNPJ Inválido');
        header('Location: ../cadastro.php?tipo=empresa');
        exit;
    }

    $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);
    $cnpjFormatado = formatarCNPJ($cnpjLimpo);

    try {
        $existeCnpj = dbFetchOne("SELECT id FROM empresas WHERE REPLACE(REPLACE(REPLACE(REPLACE(cnpj, '.', ''), '/', ''), '-', ''), ' ', '') = ? LIMIT 1", [$cnpjLimpo]);
        if ($existeCnpj) {
            definirToast('warning', 'Este CNPJ já está cadastrado. Realize o login.', 'CNPJ Existente');
            header('Location: ../login.php?tipo=empresa');
            exit;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO empresas (razao_social, nome_fantasia, cnpj, email, telefone, endereco, cidade, senha)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        dbExecute($sql, [$razaoSocial, $nomeFantasia, $cnpjFormatado, $email, $telefone, $endereco, $cidade, $senhaHash]);
        $novoId = dbLastInsertId();

        $empresaCriada = dbFetchOne("SELECT * FROM empresas WHERE id = ?", [$novoId]);
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_tipo'] = 'empresa';
        $_SESSION['usuario_dados'] = $empresaCriada;

        definirToast('success', 'Empresa cadastrada com sucesso! Pronto para criar entregas.', 'Empresa Registrada');
        header('Location: ../dashboard.php');
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao cadastrar empresa: ' . $e->getMessage(), 'Erro no Banco');
        header('Location: ../cadastro.php?tipo=empresa');
        exit;
    }
}

// Ação desconhecida
header('Location: ../index.php');
exit;
