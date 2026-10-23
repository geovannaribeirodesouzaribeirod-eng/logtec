<?php
/**
 * LogTec - Processador de Ações de Pedidos e Entregas
 *
 * Atende aos Objetivos:
 * - Entregas em tempo real
 * - Rotas de entrega
 * - Pedidos pendentes e entregas
 */

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../bd.php';

exigirLogin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$tipoUsuario = tipoUsuario();
$dadosUsuario = getUsuarioAtual();

if ($acao === 'criar_pedido') {
    if ($tipoUsuario !== 'empresa') {
        definirToast('error', 'Apenas empresas podem criar novos pedidos de entrega.', 'Acesso Negado');
        header('Location: ../dashboard.php');
        exit;
    }

    $clienteNome = trim($_POST['cliente_nome'] ?? '');
    $clienteTelefone = trim($_POST['cliente_telefone'] ?? '');
    $origem = trim($_POST['origem_endereco'] ?? '');
    $destino = trim($_POST['destino_endereco'] ?? '');
    $descricao = trim($_POST['descricao_itens'] ?? '');
    $valorFrete = floatval(str_replace(',', '.', $_POST['valor_frete'] ?? '18.50'));
    $prazo = trim($_POST['prazo_estimado'] ?? '30 - 45 min');

    if (empty($clienteNome) || empty($origem) || empty($destino) || empty($descricao)) {
        definirToast('error', 'Preencha todos os campos do pedido de entrega.', 'Atenção');
        header('Location: ../dashboard.php?modal=novo_pedido');
        exit;
    }

    try {
        $codigoRastreio = 'LOG-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

        // Coordenadas simuladas para o mapa (em torno de SP)
        $origemLat = -23.550520 + (rand(-30, 30) / 1000);
        $origemLng = -46.633308 + (rand(-30, 30) / 1000);
        $destinoLat = -23.561414 + (rand(-40, 40) / 1000);
        $destinoLng = -46.655881 + (rand(-40, 40) / 1000);

        $sql = "INSERT INTO pedidos (empresa_id, codigo_rastreio, cliente_nome, cliente_telefone, origem_endereco, origem_lat, origem_lng, destino_endereco, destino_lat, destino_lng, descricao_itens, valor_frete, prazo_estimado, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente')";
        
        dbExecute($sql, [
            $dadosUsuario['id'],
            $codigoRastreio,
            $clienteNome,
            $clienteTelefone,
            $origem,
            $origemLat,
            $origemLng,
            $destino,
            $destinoLat,
            $destinoLng,
            $descricao,
            $valorFrete,
            $prazo
        ]);

        definirToast('success', 'Pedido criado com sucesso! Código: ' . $codigoRastreio, 'Pedido Pendente Registrado');
        header('Location: ../dashboard.php');
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Falha ao registrar pedido: ' . $e->getMessage(), 'Erro no Banco');
        header('Location: ../dashboard.php');
        exit;
    }
}

elseif ($acao === 'aceitar_pedido') {
    if ($tipoUsuario !== 'funcionario') {
        definirToast('error', 'Apenas entregadores podem aceitar rotas.', 'Acesso Negado');
        header('Location: ../dashboard.php');
        exit;
    }

    $pedidoId = intval($_POST['pedido_id'] ?? $_GET['pedido_id'] ?? 0);

    try {
        // Verifica se o pedido ainda está pendente
        $pedido = dbFetchOne("SELECT * FROM pedidos WHERE id = ? AND status = 'pendente'", [$pedidoId]);

        if (!$pedido) {
            definirToast('warning', 'Este pedido já foi aceito por outro entregador ou cancelado.', 'Pedido Indisponível');
            header('Location: ../rotas.php');
            exit;
        }

        // Atribui ao entregador e coloca em trânsito
        dbExecute("UPDATE pedidos SET usuario_id = ?, status = 'em_transito' WHERE id = ?", [$dadosUsuario['id'], $pedidoId]);
        dbExecute("UPDATE usuarios SET status = 'em_entrega' WHERE id = ?", [$dadosUsuario['id']]);

        definirToast('success', 'Entrega aceita! A rota foi traçada no seu mapa.', 'Rota Iniciada');
        header('Location: ../rotas.php?pedido=' . $pedidoId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao aceitar pedido: ' . $e->getMessage(), 'Erro');
        header('Location: ../rotas.php');
        exit;
    }
}

elseif ($acao === 'atualizar_status') {
    $pedidoId = intval($_POST['pedido_id'] ?? 0);
    $novoStatus = $_POST['status'] ?? '';

    $statusValidos = ['pendente', 'em_transito', 'entregue', 'cancelado'];
    if (!in_array($novoStatus, $statusValidos)) {
        definirToast('error', 'Status inválido informado.', 'Erro');
        header('Location: ../rotas.php');
        exit;
    }

    try {
        dbExecute("UPDATE pedidos SET status = ? WHERE id = ?", [$novoStatus, $pedidoId]);

        if ($novoStatus === 'entregue' && $tipoUsuario === 'funcionario') {
            dbExecute("UPDATE usuarios SET status = 'disponivel' WHERE id = ?", [$dadosUsuario['id']]);
            definirToast('success', 'Parabéns! Entrega concluída e confirmada com o cliente.', 'Entrega Finalizada');
        } else {
            definirToast('info', 'Status do pedido atualizado para: ' . ucfirst(str_replace('_', ' ', $novoStatus)), 'Status Atualizado');
        }

        header('Location: ../rotas.php?pedido=' . $pedidoId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao atualizar status: ' . $e->getMessage(), 'Erro');
        header('Location: ../rotas.php');
        exit;
    }
}

header('Location: ../dashboard.php');
exit;
