<?php
/**
 * LogTec - Processador de Vagas de Emprego e Candidaturas
 *
 * Atende ao Requisito do CORE:
 * "terá duas maneiras de entrar, como empresa (entrando com cnpj), e como funcionário a procura de emprego."
 */

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../bd.php';

exigirLogin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$tipoUsuario = tipoUsuario();
$dadosUsuario = getUsuarioAtual();

if ($acao === 'criar_vaga') {
    if ($tipoUsuario !== 'empresa') {
        definirToast('error', 'Apenas empresas podem publicar oportunidades.', 'Acesso Negado');
        header('Location: ../vagas.php');
        exit;
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $requisitos = trim($_POST['requisitos'] ?? 'CNH A/B, responsabilidade e smartphone');
    $tipoContrato = trim($_POST['tipo_contrato'] ?? 'Entregador Parceiro');
    $remuneracao = trim($_POST['remuneracao'] ?? 'R$ 180 a R$ 250 / dia');

    if (empty($titulo) || empty($descricao)) {
        definirToast('error', 'Preencha o título e a descrição da vaga.', 'Campos Obrigatórios');
        header('Location: ../vagas.php');
        exit;
    }

    try {
        $sql = "INSERT INTO vagas (empresa_id, titulo, descricao, requisitos, tipo_contrato, remuneracao, status)
                VALUES (?, ?, ?, ?, ?, ?, 'aberta')";
        dbExecute($sql, [$dadosUsuario['id'], $titulo, $descricao, $requisitos, $tipoContrato, $remuneracao]);

        definirToast('success', 'Nova vaga publicada com sucesso!', 'Vaga Aberta');
        header('Location: ../vagas.php');
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao publicar vaga: ' . $e->getMessage(), 'Erro');
        header('Location: ../vagas.php');
        exit;
    }
}

elseif ($acao === 'candidatar') {
    if ($tipoUsuario !== 'funcionario') {
        definirToast('error', 'Apenas funcionários/entregadores podem se candidatar às vagas.', 'Acesso Negado');
        header('Location: ../vagas.php');
        exit;
    }

    $vagaId = intval($_POST['vaga_id'] ?? 0);
    $mensagem = trim($_POST['mensagem'] ?? 'Olá, tenho interesse nesta vaga e cumpro todos os requisitos.');

    try {
        // Verifica se já se candidatou
        $jaCandidatou = dbFetchOne("SELECT id FROM candidaturas WHERE vaga_id = ? AND usuario_id = ?", [$vagaId, $dadosUsuario['id']]);
        if ($jaCandidatou) {
            definirToast('warning', 'Você já enviou sua candidatura para esta oportunidade.', 'Já Candidatado');
            header('Location: ../vagas.php');
            exit;
        }

        $sql = "INSERT INTO candidaturas (vaga_id, usuario_id, mensagem, status) VALUES (?, ?, ?, 'pendente')";
        dbExecute($sql, [$vagaId, $dadosUsuario['id'], $mensagem]);

        definirToast('success', 'Candidatura enviada com sucesso! A empresa entrará em contato.', 'Candidatura Registrada');
        header('Location: ../vagas.php');
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao enviar candidatura: ' . $e->getMessage(), 'Erro');
        header('Location: ../vagas.php');
        exit;
    }
}

header('Location: ../vagas.php');
exit;
