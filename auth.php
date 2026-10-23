<?php
/**
 * LogTec - Módulo de Autenticação, Sessões e Validações de Negócio
 *
 * Regras Atendidas:
 * - Sessões nativas do PHP
 * - Validação de CPF único e com cálculo de dígitos verificadores
 * - Validação de CNPJ para contas de Empresa
 * - Validação estrita de Maioridade (>= 18 anos)
 * - Sistema de Flash Toast messages
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/bd.php';

/**
 * Retorna se há usuário logado
 */
function usuarioLogado() {
    return isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
}

/**
 * Retorna o tipo da sessão atual: 'empresa' ou 'funcionario'
 */
function tipoUsuario() {
    return $_SESSION['usuario_tipo'] ?? null;
}

/**
 * Retorna os dados do usuário autenticado
 */
function getUsuarioAtual() {
    return $_SESSION['usuario_dados'] ?? null;
}

/**
 * Redireciona caso o usuário não esteja logado ou não tenha permissão
 */
function exigirLogin($tipoPermitido = null) {
    if (!usuarioLogado()) {
        definirToast('warning', 'Você precisa realizar login para acessar esta página.', 'Acesso Restrito');
        header('Location: login.php');
        exit;
    }

    if ($tipoPermitido !== null && tipoUsuario() !== $tipoPermitido) {
        definirToast('error', 'Acesso não autorizado para o seu perfil.', 'Permissão Negada');
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Armazena uma mensagem flash de Toast para a próxima requisição
 *
 * @param string $tipo 'success', 'error', 'warning', 'info'
 * @param string $mensagem Texto da notificação
 * @param string $titulo Título opcional
 */
function definirToast($tipo, $mensagem, $titulo = '') {
    $_SESSION['toast'] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem,
        'titulo' => $titulo
    ];
}

/**
 * Recupera e consome a notificação de Toast atual
 */
function obterToast() {
    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        unset($_SESSION['toast']);
        return $toast;
    }
    return null;
}

/**
 * Validação rigorosa de Maioridade (Regra do requisitos.md: "precisa ter a maior idade")
 *
 * @param string $dataNasc Data no formato Y-m-d
 * @return bool
 */
function validarMaioridade($dataNasc) {
    if (empty($dataNasc)) {
        return false;
    }

    $nascimento = DateTime::createFromFormat('Y-m-d', $dataNasc);
    if (!$nascimento) {
        return false;
    }

    $hoje = new DateTime();
    $idade = $hoje->diff($nascimento)->y;

    return $idade >= 18;
}

/**
 * Validação de CPF com cálculo dos dígitos verificadores (Regra: um único usuário por CPF)
 */
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Deve ter 11 dígitos
    if (strlen($cpf) !== 11) {
        return false;
    }

    // Rejeita sequências repetidas como 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Valida primeiro dígito
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}

/**
 * Validação de CNPJ para acesso de Empresas
 */
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if (strlen($cnpj) !== 14) {
        return false;
    }

    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    // Valida primeiro dígito
    $tamanho = 12;
    $numeros = substr($cnpj, 0, $tamanho);
    $digitos = substr($cnpj, $tamanho);
    $soma = 0;
    $pos = $tamanho - 7;
    for ($i = $tamanho; $i >= 1; $i--) {
        $soma += $numeros[$tamanho - $i] * $pos--;
        if ($pos < 2) {
            $pos = 9;
        }
    }
    $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
    if ($resultado != $digitos[0]) {
        return false;
    }

    // Valida segundo dígito
    $tamanho = 13;
    $numeros = substr($cnpj, 0, $tamanho);
    $soma = 0;
    $pos = $tamanho - 7;
    for ($i = $tamanho; $i >= 1; $i--) {
        $soma += $numeros[$tamanho - $i] * $pos--;
        if ($pos < 2) {
            $pos = 9;
        }
    }
    $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
    if ($resultado != $digitos[1]) {
        return false;
    }

    return true;
}

/**
 * Formata CPF para exibição
 */
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) === 11) {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
    return $cpf;
}

/**
 * Formata CNPJ para exibição
 */
function formatarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) === 14) {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
    return $cnpj;
}
