-- =====================================================================
-- LOGTEC - Sistema de Entregas e Gestão Logística
-- Banco de Dados: MySQL (compatível com XAMPP, InfinityFree, Laragon, etc)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `logtec` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `logtec`;

-- 1. Tabela de Usuários (Funcionários / Entregadores a procura de emprego)
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `cpf` VARCHAR(14) NOT NULL UNIQUE,
    `data_nascimento` DATE NOT NULL,
    `telefone` VARCHAR(20) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `status` ENUM('disponivel', 'em_entrega', 'indisponivel') DEFAULT 'disponivel',
    `veiculo` VARCHAR(50) DEFAULT 'Moto',
    `placa` VARCHAR(20) DEFAULT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabela de Empresas (Cadastro com CNPJ)
CREATE TABLE IF NOT EXISTS `empresas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `razao_social` VARCHAR(150) NOT NULL,
    `nome_fantasia` VARCHAR(150) NOT NULL,
    `cnpj` VARCHAR(18) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `telefone` VARCHAR(20) NOT NULL,
    `endereco` VARCHAR(255) NOT NULL,
    `cidade` VARCHAR(100) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabela de Pedidos / Entregas em Tempo Real
CREATE TABLE IF NOT EXISTS `pedidos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `empresa_id` INT NOT NULL,
    `usuario_id` INT DEFAULT NULL,
    `codigo_rastreio` VARCHAR(20) NOT NULL UNIQUE,
    `cliente_nome` VARCHAR(150) NOT NULL,
    `cliente_telefone` VARCHAR(20) NOT NULL,
    `origem_endereco` VARCHAR(255) NOT NULL,
    `origem_lat` DECIMAL(10, 8) DEFAULT -23.550520,
    `origem_lng` DECIMAL(11, 8) DEFAULT -46.633308,
    `destino_endereco` VARCHAR(255) NOT NULL,
    `destino_lat` DECIMAL(10, 8) DEFAULT -23.561414,
    `destino_lng` DECIMAL(11, 8) DEFAULT -46.655881,
    `descricao_itens` TEXT NOT NULL,
    `valor_frete` DECIMAL(10, 2) NOT NULL DEFAULT 18.50,
    `prazo_estimado` VARCHAR(50) DEFAULT '35 min',
    `status` ENUM('pendente', 'em_transito', 'entregue', 'cancelado') DEFAULT 'pendente',
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pedidos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabela de Listas (Templates de Listas com ícone e cores alteráveis)
CREATE TABLE IF NOT EXISTS `listas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo_autor` ENUM('usuario', 'empresa') NOT NULL,
    `autor_id` INT NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `categoria` VARCHAR(50) NOT NULL DEFAULT 'geral',
    `icone` VARCHAR(50) NOT NULL DEFAULT 'fa-basket-shopping',
    `cor` VARCHAR(30) DEFAULT 'blue',
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabela de Itens de cada Lista
CREATE TABLE IF NOT EXISTS `itens_lista` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `lista_id` INT NOT NULL,
    `descricao` VARCHAR(255) NOT NULL,
    `concluido` TINYINT(1) DEFAULT 0,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_itens_lista` FOREIGN KEY (`lista_id`) REFERENCES `listas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabela de Vagas de Emprego
CREATE TABLE IF NOT EXISTS `vagas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `empresa_id` INT NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `descricao` TEXT NOT NULL,
    `requisitos` VARCHAR(255) DEFAULT 'CNH A/B em dia, responsabilidade e smartphone com internet',
    `tipo_contrato` VARCHAR(50) DEFAULT 'Entregador Parceiro (Diárias)',
    `remuneracao` VARCHAR(50) DEFAULT 'R$ 150 a R$ 250 / dia',
    `status` ENUM('aberta', 'encerrada') DEFAULT 'aberta',
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_vagas_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabela de Candidaturas a Emprego
CREATE TABLE IF NOT EXISTS `candidaturas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vaga_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `mensagem` TEXT,
    `status` ENUM('pendente', 'aceita', 'recusada') DEFAULT 'pendente',
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_candidaturas_vaga` FOREIGN KEY (`vaga_id`) REFERENCES `vagas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_candidaturas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- DADOS INICIAIS DE DEMONSTRAÇÃO (Senha padrão para testes: 123456)
-- Hash bcrypt de '123456': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- =====================================================================

-- Inserindo Empresa de Teste
INSERT INTO `empresas` (`id`, `razao_social`, `nome_fantasia`, `cnpj`, `email`, `telefone`, `endereco`, `cidade`, `senha`)
VALUES
(1, 'LogTec Soluções Logísticas LTDA', 'LogTec Express', '12.345.678/0001-99', 'contato@logtec.com', '(11) 98765-4321', 'Av. Paulista, 1000 - Bela Vista', 'São Paulo - SP', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Inserindo Usuário/Entregador de Teste (+18 anos, CPF único)
INSERT INTO `usuarios` (`id`, `nome`, `email`, `cpf`, `data_nascimento`, `telefone`, `senha`, `status`, `veiculo`, `placa`)
VALUES
(1, 'Carlos Silva Entregador', 'carlos@logtec.com', '123.456.789-00', '1998-05-15', '(11) 97777-8888', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'disponivel', 'Moto Honda CG 160', 'BRA2E19')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Inserindo Pedidos de Teste (Pendente e Em Rota)
INSERT INTO `pedidos` (`id`, `empresa_id`, `usuario_id`, `codigo_rastreio`, `cliente_nome`, `cliente_telefone`, `origem_endereco`, `origem_lat`, `origem_lng`, `destino_endereco`, `destino_lat`, `destino_lng`, `descricao_itens`, `valor_frete`, `prazo_estimado`, `status`)
VALUES
(1, 1, NULL, 'LOG-2026-9812', 'Mariana Santos', '(11) 91234-5678', 'Hub Central LogTec - Av. Paulista, 1000', -23.561414, -46.655881, 'Rua Augusta, 1500 - Consolação', -23.553280, -46.658740, '1x Pacote Eletrônicos (Notebook Dell + Acessórios)', 24.50, '25 min', 'pendente'),
(2, 1, 1, 'LOG-2026-9813', 'Roberto Oliveira', '(11) 99876-1122', 'Centro de Distribuição Norte - Rua Voluntários, 500', -23.512300, -46.627800, 'Rua Vergueiro, 3185 - Vila Mariana', -23.585500, -46.635800, 'Caixa com Medicamentos e Suprimentos de Saúde', 32.00, '40 min', 'em_transito')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Inserindo Templates de Listas solicitados no requisitos.md
-- (Mercado, Músicas, Filmes, Viagens, cada uma com ícone diferente)
INSERT INTO `listas` (`id`, `tipo_autor`, `autor_id`, `titulo`, `categoria`, `icone`, `cor`)
VALUES
(1, 'usuario', 1, 'Lista de Mercado da Semana', 'mercado', 'fa-cart-shopping', 'emerald'),
(2, 'usuario', 1, 'Minhas Músicas para Rotas', 'musica', 'fa-music', 'violet'),
(3, 'usuario', 1, 'Filmes para Assistir no Fim de Semana', 'filmes', 'fa-film', 'amber'),
(4, 'usuario', 1, 'Planejamento de Viagem de Férias', 'viagem', 'fa-plane-departure', 'sky')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Itens das Listas
INSERT INTO `itens_lista` (`lista_id`, `descricao`, `concluido`) VALUES
(1, 'Frutas: Banana, Maçã e Laranja', 1),
(1, 'Café solúvel e Leite desnatado', 1),
(1, 'Barras de cereal para o dia de entregas', 0),
(1, 'Garrafa de água térmica 1L', 0),
(2, 'Lo-fi Beats para trânsito calmo', 1),
(2, 'Rock Clássico Anos 80 e 90', 0),
(2, 'Podcasts de Notícias e Tecnologia', 0),
(3, 'Interestelar (Ficção Científica)', 1),
(3, 'O Poderoso Chefão (Clássico)', 0),
(3, 'Mad Max: Estrada da Fúria', 0),
(4, 'Revisar pneus e freios do veículo', 1),
(4, 'Reservar pousada em Ubatuba', 0),
(4, 'Separar kit de primeiros socorros', 0);

-- Vaga de Emprego
INSERT INTO `vagas` (`id`, `empresa_id`, `titulo`, `descricao`, `tipo_contrato`, `remuneracao`, `status`)
VALUES
(1, 1, 'Entregador Motociclista - Região Central', 'Procuramos entregadores dedicados com moto própria para atendimento de demandas expressas no centro expandido de SP.', 'Entregador Parceiro (MEI)', 'R$ 180 a R$ 260 por dia', 'aberta')
ON DUPLICATE KEY UPDATE `id`=`id`;
