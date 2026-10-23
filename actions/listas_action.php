<?php
/**
 * LogTec - Processador de Ações para Templates de Listas
 *
 * Atende ao Requisito do CORE:
 * "o sistema terá vários templates de listas, ex: listas de mercado, listas de musicas, de filmes viagens,
 *  cada uma com um ícone diferente que o usuário pode alterar."
 */

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../bd.php';

exigirLogin();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$tipoUsuario = tipoUsuario();
$dadosUsuario = getUsuarioAtual();
$autorId = $dadosUsuario['id'];

if ($acao === 'criar_lista') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? 'geral');
    $icone = trim($_POST['icone'] ?? 'fa-basket-shopping');
    $cor = trim($_POST['cor'] ?? 'blue');

    if (empty($titulo)) {
        definirToast('error', 'O título da lista é obrigatório.', 'Campo Obrigatório');
        header('Location: ../listas.php');
        exit;
    }

    try {
        $sql = "INSERT INTO listas (tipo_autor, autor_id, titulo, categoria, icone, cor) VALUES (?, ?, ?, ?, ?, ?)";
        dbExecute($sql, [$tipoUsuario, $autorId, $titulo, $categoria, $icone, $cor]);
        $listaId = dbLastInsertId();

        // Adiciona itens iniciais de sugestão se for template
        $itensIniciais = [];
        if ($categoria === 'mercado') {
            $itensIniciais = ['Frutas e Legumes', 'Leite e Derivados', 'Pão e Grãos'];
        } elseif ($categoria === 'musica') {
            $itensIniciais = ['Música de Abertura da Rota', 'Músicas Favoritas de Viagem'];
        } elseif ($categoria === 'filmes') {
            $itensIniciais = ['Filme para o Sábado à Noite', 'Documentário sobre Logística'];
        } elseif ($categoria === 'viagem') {
            $itensIniciais = ['Checar combustível e calibragem', 'Separar documentos e CNH'];
        }

        foreach ($itensIniciais as $item) {
            dbExecute("INSERT INTO itens_lista (lista_id, descricao, concluido) VALUES (?, ?, 0)", [$listaId, $item]);
        }

        definirToast('success', 'Nova lista criada com sucesso!', 'Lista Adicionada');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao criar lista: ' . $e->getMessage(), 'Erro');
        header('Location: ../listas.php');
        exit;
    }
}

elseif ($acao === 'alterar_icone') {
    // Permite que o usuário altere o ícone da lista (Requisito explícito)
    $listaId = intval($_POST['lista_id'] ?? 0);
    $novoIcone = trim($_POST['icone'] ?? 'fa-list-check');
    $novaCor = trim($_POST['cor'] ?? 'blue');
    $novoTitulo = trim($_POST['titulo'] ?? '');

    try {
        $params = [$novoIcone, $novaCor];
        $sql = "UPDATE listas SET icone = ?, cor = ?";
        if (!empty($novoTitulo)) {
            $sql .= ", titulo = ?";
            $params[] = $novoTitulo;
        }
        $sql .= " WHERE id = ? AND tipo_autor = ? AND autor_id = ?";
        $params[] = $listaId;
        $params[] = $tipoUsuario;
        $params[] = $autorId;

        dbExecute($sql, $params);
        definirToast('success', 'Ícone e estilo da lista atualizados!', 'Customização Salva');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao alterar ícone: ' . $e->getMessage(), 'Erro');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;
    }
}

elseif ($acao === 'excluir_lista') {
    $listaId = intval($_POST['lista_id'] ?? $_GET['lista_id'] ?? 0);

    try {
        dbExecute("DELETE FROM listas WHERE id = ? AND tipo_autor = ? AND autor_id = ?", [$listaId, $tipoUsuario, $autorId]);
        definirToast('info', 'A lista selecionada foi excluída.', 'Lista Removida');
        header('Location: ../listas.php');
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao remover lista: ' . $e->getMessage(), 'Erro');
        header('Location: ../listas.php');
        exit;
    }
}

elseif ($acao === 'adicionar_item') {
    $listaId = intval($_POST['lista_id'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');

    if (empty($descricao)) {
        definirToast('warning', 'Digite a descrição do item a adicionar.', 'Item Vazio');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;
    }

    try {
        // Valida se a lista pertence ao usuário
        $lista = dbFetchOne("SELECT id FROM listas WHERE id = ? AND tipo_autor = ? AND autor_id = ?", [$listaId, $tipoUsuario, $autorId]);
        if (!$lista) {
            definirToast('error', 'Lista não encontrada.', 'Erro');
            header('Location: ../listas.php');
            exit;
        }

        dbExecute("INSERT INTO itens_lista (lista_id, descricao, concluido) VALUES (?, ?, 0)", [$listaId, $descricao]);
        definirToast('success', 'Item adicionado à sua lista!', 'Item Salvo');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao adicionar item: ' . $e->getMessage(), 'Erro');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;
    }
}

elseif ($acao === 'toggle_item') {
    $itemId = intval($_POST['item_id'] ?? $_GET['item_id'] ?? 0);
    $listaId = intval($_POST['lista_id'] ?? $_GET['lista_id'] ?? 0);

    try {
        $item = dbFetchOne("SELECT * FROM itens_lista WHERE id = ?", [$itemId]);
        if ($item) {
            $novoStatus = $item['concluido'] ? 0 : 1;
            dbExecute("UPDATE itens_lista SET concluido = ? WHERE id = ?", [$novoStatus, $itemId]);
        }
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao atualizar item: ' . $e->getMessage(), 'Erro');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;
    }
}

elseif ($acao === 'excluir_item') {
    $itemId = intval($_POST['item_id'] ?? $_GET['item_id'] ?? 0);
    $listaId = intval($_POST['lista_id'] ?? $_GET['lista_id'] ?? 0);

    try {
        dbExecute("DELETE FROM itens_lista WHERE id = ?", [$itemId]);
        definirToast('info', 'Item excluído.', 'Removido');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;

    } catch (Exception $e) {
        definirToast('error', 'Erro ao excluir item: ' . $e->getMessage(), 'Erro');
        header('Location: ../listas.php?lista_id=' . $listaId);
        exit;
    }
}

header('Location: ../listas.php');
exit;
