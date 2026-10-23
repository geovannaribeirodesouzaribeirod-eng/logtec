<?php
/**
 * LogTec - Conexão Segura com Banco de Dados via PDO
 *
 * Regras Globais Atendidas:
 * - Singleton: Garante exatamente a mesma instância do PDO em toda a execução.
 * - Prepared Statements para evitar SQL Injection em todas as queries.
 * - Suporte a credenciais locais (XAMPP/Laragon) e hospedagens remotas (InfinityFree).
 * - Fallback automático para SQLite caso o serviço MySQL esteja temporariamente offline.
 */

// Configurações do Banco de Dados
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'logtec');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

/**
 * Retorna a instância única do PDO (Singleton)
 *
 * @return PDO
 */
function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Caso o MySQL local não esteja rodando, utiliza SQLite local como fallback transparente
            // Isso permite que o projeto funcione imediatamente mesmo sem XAMPP/MySQL ativo
            $sqlitePath = __DIR__ . '/logtec.sqlite';
            $isNewDb = !file_exists($sqlitePath);
            $pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            if ($isNewDb) {
                inicializarBancoSQLite($pdo);
            }
        }
    }

    return $pdo;
}

/**
 * Executa uma consulta preparada e retorna o Statement
 *
 * @param string $sql
 * @param array $params
 * @return PDOStatement
 */
function dbQuery($sql, $params = []) {
    $pdo = getDB();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Retorna todos os registros de uma consulta
 */
function dbFetchAll($sql, $params = []) {
    return dbQuery($sql, $params)->fetchAll();
}

/**
 * Retorna um único registro
 */
function dbFetchOne($sql, $params = []) {
    return dbQuery($sql, $params)->fetch();
}

/**
 * Executa um comando (INSERT, UPDATE, DELETE) e retorna o número de linhas afetadas
 */
function dbExecute($sql, $params = []) {
    return dbQuery($sql, $params)->rowCount();
}

/**
 * Retorna o ID do último registro inserido
 */
function dbLastInsertId() {
    return getDB()->lastInsertId();
}

/**
 * Inicializa estrutura e dados no SQLite caso MySQL não esteja disponível
 */
function inicializarBancoSQLite($pdo) {
    $schema = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        cpf TEXT NOT NULL UNIQUE,
        data_nascimento TEXT NOT NULL,
        telefone TEXT NOT NULL,
        senha TEXT NOT NULL,
        status TEXT DEFAULT 'disponivel',
        veiculo TEXT DEFAULT 'Moto',
        placa TEXT DEFAULT NULL,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS empresas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        razao_social TEXT NOT NULL,
        nome_fantasia TEXT NOT NULL,
        cnpj TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        telefone TEXT NOT NULL,
        endereco TEXT NOT NULL,
        cidade TEXT NOT NULL,
        senha TEXT NOT NULL,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS pedidos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        empresa_id INTEGER NOT NULL,
        usuario_id INTEGER DEFAULT NULL,
        codigo_rastreio TEXT NOT NULL UNIQUE,
        cliente_nome TEXT NOT NULL,
        cliente_telefone TEXT NOT NULL,
        origem_endereco TEXT NOT NULL,
        origem_lat REAL DEFAULT -23.550520,
        origem_lng REAL DEFAULT -46.633308,
        destino_endereco TEXT NOT NULL,
        destino_lat REAL DEFAULT -23.561414,
        destino_lng REAL DEFAULT -46.655881,
        descricao_itens TEXT NOT NULL,
        valor_frete REAL DEFAULT 18.50,
        prazo_estimado TEXT DEFAULT '35 min',
        status TEXT DEFAULT 'pendente',
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS listas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tipo_autor TEXT NOT NULL,
        autor_id INTEGER NOT NULL,
        titulo TEXT NOT NULL,
        categoria TEXT DEFAULT 'geral',
        icone TEXT DEFAULT 'fa-cart-shopping',
        cor TEXT DEFAULT 'blue',
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS itens_lista (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        lista_id INTEGER NOT NULL,
        descricao TEXT NOT NULL,
        concluido INTEGER DEFAULT 0,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS vagas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        empresa_id INTEGER NOT NULL,
        titulo TEXT NOT NULL,
        descricao TEXT NOT NULL,
        requisitos TEXT DEFAULT 'CNH A/B em dia e veículo próprio',
        tipo_contrato TEXT DEFAULT 'Entregador Parceiro',
        remuneracao TEXT DEFAULT 'R$ 180 a R$ 260 / dia',
        status TEXT DEFAULT 'aberta',
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS candidaturas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        vaga_id INTEGER NOT NULL,
        usuario_id INTEGER NOT NULL,
        mensagem TEXT,
        status TEXT DEFAULT 'pendente',
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($schema);

    // Hash da senha '123456'
    $senhaHash = password_hash('123456', PASSWORD_DEFAULT);

    // Inserção inicial
    $pdo->exec("INSERT OR IGNORE INTO empresas (id, razao_social, nome_fantasia, cnpj, email, telefone, endereco, cidade, senha)
                VALUES (1, 'LogTec Soluções Logísticas LTDA', 'LogTec Express', '12.345.678/0001-99', 'contato@logtec.com', '(11) 98765-4321', 'Av. Paulista, 1000 - Bela Vista', 'São Paulo - SP', '{$senhaHash}')");

    $pdo->exec("INSERT OR IGNORE INTO usuarios (id, nome, email, cpf, data_nascimento, telefone, senha, status, veiculo, placa)
                VALUES (1, 'Carlos Silva Entregador', 'carlos@logtec.com', '123.456.789-00', '1998-05-15', '(11) 97777-8888', '{$senhaHash}', 'disponivel', 'Moto Honda CG 160', 'BRA2E19')");

    $pdo->exec("INSERT OR IGNORE INTO pedidos (id, empresa_id, usuario_id, codigo_rastreio, cliente_nome, cliente_telefone, origem_endereco, destino_endereco, descricao_itens, valor_frete, prazo_estimado, status)
                VALUES (1, 1, NULL, 'LOG-2026-9812', 'Mariana Santos', '(11) 91234-5678', 'Hub Central - Av. Paulista, 1000', 'Rua Augusta, 1500 - Consolação', '1x Notebook Dell + Acessórios', 24.50, '25 min', 'pendente')");

    $pdo->exec("INSERT OR IGNORE INTO pedidos (id, empresa_id, usuario_id, codigo_rastreio, cliente_nome, cliente_telefone, origem_endereco, destino_endereco, descricao_itens, valor_frete, prazo_estimado, status)
                VALUES (2, 1, 1, 'LOG-2026-9813', 'Roberto Oliveira', '(11) 99876-1122', 'CD Norte - Rua Voluntários, 500', 'Rua Vergueiro, 3185', 'Caixa com Medicamentos Express', 32.00, '40 min', 'em_transito')");

    // Templates de listas padrão conforme o requisito
    $pdo->exec("INSERT OR IGNORE INTO listas (id, tipo_autor, autor_id, titulo, categoria, icone, cor)
                VALUES (1, 'usuario', 1, 'Lista de Mercado da Semana', 'mercado', 'fa-cart-shopping', 'emerald')");
    $pdo->exec("INSERT OR IGNORE INTO listas (id, tipo_autor, autor_id, titulo, categoria, icone, cor)
                VALUES (2, 'usuario', 1, 'Minhas Músicas para Rotas', 'musica', 'fa-music', 'violet')");
    $pdo->exec("INSERT OR IGNORE INTO listas (id, tipo_autor, autor_id, titulo, categoria, icone, cor)
                VALUES (3, 'usuario', 1, 'Filmes para Assistir no Fim de Semana', 'filmes', 'fa-film', 'amber')");
    $pdo->exec("INSERT OR IGNORE INTO listas (id, tipo_autor, autor_id, titulo, categoria, icone, cor)
                VALUES (4, 'usuario', 1, 'Planejamento de Viagem de Férias', 'viagem', 'fa-plane-departure', 'sky')");

    $pdo->exec("INSERT OR IGNORE INTO itens_lista (lista_id, descricao, concluido) VALUES
                (1, 'Frutas: Banana, Maçã e Laranja', 1),
                (1, 'Café solúvel e Leite desnatado', 1),
                (1, 'Barras de cereal para o dia de entregas', 0),
                (2, 'Lo-fi Beats para trânsito calmo', 1),
                (2, 'Rock Clássico Anos 80 e 90', 0),
                (3, 'Interestelar (Ficção Científica)', 1),
                (3, 'Mad Max: Estrada da Fúria', 0),
                (4, 'Revisar pneus e freios do veículo', 1),
                (4, 'Reservar pousada em Ubatuba', 0)");

    $pdo->exec("INSERT OR IGNORE INTO vagas (id, empresa_id, titulo, descricao, remuneracao, status)
                VALUES (1, 1, 'Entregador Motociclista - Região Central', 'Entregas pontuais e rápidas no centro de SP.', 'R$ 180 a R$ 260 / dia', 'aberta')");
}
