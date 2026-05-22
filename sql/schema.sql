-- IndusOS Food schema
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) UNIQUE,
  cpf VARCHAR(14) UNIQUE,
  cnpj VARCHAR(18) UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  papel ENUM('admin','gestor','producao','expedicao','financeiro','cliente','qualidade') NOT NULL,
  is_ativo TINYINT(1) DEFAULT 1,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNIQUE,
  razao_social VARCHAR(160),
  nome_fantasia VARCHAR(160),
  cnpj VARCHAR(18) UNIQUE,
  cpf VARCHAR(14) UNIQUE,
  ie VARCHAR(30),
  im VARCHAR(30),
  email VARCHAR(160),
  telefone VARCHAR(30),
  contato_tipo ENUM('celular','telefone') DEFAULT 'celular',
  whatsapp VARCHAR(30),
  cep VARCHAR(9),
  endereco VARCHAR(160),
  numero VARCHAR(20),
  bairro VARCHAR(120),
  cidade VARCHAR(120),
  estado VARCHAR(2),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_clientes_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  sku VARCHAR(50) NULL,
  produto_pa_id INT NULL,
  yield_qty DECIMAL(10,3) NOT NULL,
  yield_unit VARCHAR(20) NOT NULL,
  active TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50) UNIQUE NOT NULL,
  nome VARCHAR(160) NOT NULL,
  tipo ENUM('MP','PA') NOT NULL DEFAULT 'PA',
  categoria VARCHAR(80),
  recipe_id INT,
  unidade VARCHAR(10) NOT NULL,
  peso_liquido DECIMAL(10,3),
  shelf_life_dias INT DEFAULT 0,
  preco DECIMAL(12,2) DEFAULT 0,
  alergicos TEXT,
  tabela_nutricional JSON,
  is_ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS produto_componentes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  componente_id INT NOT NULL,
  quantidade DECIMAL(14,4) NOT NULL,
  unidade VARCHAR(10) NOT NULL,
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (componente_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS produtos_lancamentos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  preco DECIMAL(12,2) DEFAULT 0,
  fornecedor VARCHAR(120),
  data_entrega DATE,
  lote VARCHAR(60),
  data_fabricacao DATE,
  data_vencimento DATE,
  usuario_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS recipe_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  stock_product_id INT NOT NULL,
  qty DECIMAL(10,3) NOT NULL,
  unit VARCHAR(20) NOT NULL,
  loss_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  sort_order INT DEFAULT 0,
  FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (stock_product_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estoques (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  lote VARCHAR(60),
  validade DATE,
  localizacao VARCHAR(60),
  quantidade DECIMAL(14,4) DEFAULT 0,
  reservado DECIMAL(14,4) DEFAULT 0,
  custo_medio DECIMAL(14,6) DEFAULT 0,
  tipo ENUM('MP','PA') DEFAULT 'PA',
  UNIQUE KEY uniq_prod_lote_loc (produto_id,lote,localizacao),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS movimentos_estoque (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  tipo ENUM('entrada','saida','ajuste','transferencia') NOT NULL,
  status ENUM('efetivado','reservado') NOT NULL DEFAULT 'efetivado',
  quantidade DECIMAL(14,4) NOT NULL,
  custo_unit DECIMAL(14,6) DEFAULT 0,
  lote VARCHAR(60),
  validade DATE,
  origem VARCHAR(60),
  destino VARCHAR(60),
  ref_tipo VARCHAR(40),
  ref_id BIGINT,
  usuario_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS recipe_runs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  produced_qty DECIMAL(10,3) NOT NULL,
  total_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  sale_unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  sale_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  profit_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  margin_percent DECIMAL(6,2) NOT NULL DEFAULT 0,
  source ENUM('SALE','MANUAL','ADJUST') NOT NULL DEFAULT 'MANUAL',
  source_id BIGINT NULL,
  created_by BIGINT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_recipe_run_source (source, source_id),
  FOREIGN KEY (recipe_id) REFERENCES recipes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS recipe_run_items (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  run_id BIGINT NOT NULL,
  stock_product_id INT NOT NULL,
  qty_used DECIMAL(10,3) NOT NULL,
  cost_unit_at_time DECIMAL(12,4) NOT NULL DEFAULT 0,
  cost_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (run_id) REFERENCES recipe_runs(id) ON DELETE CASCADE,
  FOREIGN KEY (stock_product_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedidos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  codigo VARCHAR(30) UNIQUE,
  status ENUM('novo','aprovacao','producao','separacao','expedicao','faturado','cancelado') NOT NULL DEFAULT 'novo',
  total DECIMAL(14,2) DEFAULT 0,
  frete_valor DECIMAL(14,2) DEFAULT 0,
  observacoes TEXT,
  forma_pagto VARCHAR(40) DEFAULT 'PIX',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pedido_itens (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  pedido_id BIGINT NOT NULL,
  produto_id INT NOT NULL,
  quantidade DECIMAL(14,4) NOT NULL,
  preco_unit DECIMAL(14,2) NOT NULL,
  desconto DECIMAL(14,2) DEFAULT 0,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ordens_producao (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(30) UNIQUE,
  pedido_id BIGINT,
  produto_id INT NOT NULL,
  quantidade DECIMAL(14,4) NOT NULL,
  rendimento_percent DECIMAL(6,2) DEFAULT 100.00,
  lote VARCHAR(60),
  validade DATE,
  etapa ENUM('planejada','em_andamento','finalizada','cancelada') DEFAULT 'planejada',
  responsavel_id INT,
  inicio_previsto DATETIME,
  fim_previsto DATETIME,
  inicio_real DATETIME,
  fim_real DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (responsavel_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS expedicoes (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  pedido_id BIGINT NOT NULL,
  data_separacao DATETIME,
  data_expedicao DATETIME,
  volumes INT DEFAULT 1,
  peso_total DECIMAL(14,3) DEFAULT 0,
  transportadora VARCHAR(120),
  romaneio VARCHAR(40) UNIQUE,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS financeiro_titulos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('pagar','receber') NOT NULL,
  ref_tipo VARCHAR(40),
  ref_id BIGINT,
  cliente_id INT,
  descricao VARCHAR(180),
  vencimento DATE NOT NULL,
  valor DECIMAL(14,2) NOT NULL,
  valor_pago DECIMAL(14,2) DEFAULT 0,
  status ENUM('aberto','parcial','pago','vencido') DEFAULT 'aberto',
  forma_pagto ENUM('PIX','TED','Boleto','Cartao','Dinheiro') DEFAULT 'PIX',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS financeiro_lancamentos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('entrada','saida') NOT NULL,
  numero_titulo VARCHAR(60) NOT NULL,
  nome_empresa VARCHAR(180) NOT NULL,
  validade DATE NULL,
  valor DECIMAL(14,2) NULL,
  data_lancamento DATE NOT NULL,
  data_entrada DATE NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'aberto',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_financeiro_lancamentos_tipo (tipo),
  INDEX idx_financeiro_lancamentos_numero (numero_titulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS configuracoes (
  chave VARCHAR(60) PRIMARY KEY,
  valor VARCHAR(255) NOT NULL,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mapa_entradas (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  data_referencia DATE NOT NULL,
  quantidade DECIMAL(14,4) NOT NULL DEFAULT 0,
  observacao VARCHAR(180),
  usuario_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_produto_dia (produto_id, data_referencia),
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  acao VARCHAR(60),
  entidade VARCHAR(60),
  entidade_id BIGINT,
  detalhes JSON,
  ip VARCHAR(45),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE VIEW vw_estoque_a_vencer AS
SELECT e.*, p.nome, p.sku,
       DATEDIFF(e.validade, CURDATE()) AS dias_para_vencer
FROM estoques e
JOIN produtos p ON p.id = e.produto_id
WHERE e.quantidade > 0 AND e.validade IS NOT NULL;

INSERT INTO configuracoes (chave, valor) VALUES
 ('VALIDADE_ALERTA_DIAS','15')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('PEDIDO_BAIXA_NO_CRIAR','false')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('METRIC_UNITS','kg, un, cx')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('ESTOQUE_BAIXO_MIN','4'),
 ('ESTOQUE_BAIXO_MAX','8')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('SKU_DIGITS','3')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('PERFORMANCE_INCLUDE_SATURDAY','1'),
 ('PERFORMANCE_INCLUDE_SUNDAY','1')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);


INSERT INTO configuracoes (chave, valor) VALUES
 ('FINANCEIRO_ALERTA_VENCIMENTO_DIAS','7')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('LIST_PAGE_SIZE_OPTIONS','15,20,30,40,50,100'),
 ('LIST_PAGE_SIZE_DEFAULT','15'),
 ('LIST_PAGE_SIZE_DEFAULT_CLIENTES','15'),
 ('LIST_PAGE_SIZE_DEFAULT_PRODUTOS','15'),
 ('LIST_PAGE_SIZE_DEFAULT_FINANCEIRO_ENTRADA','15'),
 ('LIST_PAGE_SIZE_DEFAULT_FINANCEIRO_SAIDA','15')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('BACKUP_FULL_ENABLED','1'),
 ('BACKUP_MODULES','auth, clientes, produtos, producao, estoque, expedicao, financeiro, relatorios, config, portal')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('RBAC_MODULES','{"admin":["dashboard","clientes","produtos","pedidos","receitas","producao","estoque","expedicao","financeiro","relatorios","mapa","desempenho","meta","config","portal"],"gestor":["dashboard","clientes","produtos","pedidos","receitas","producao","estoque","expedicao","financeiro","relatorios","mapa","desempenho","meta","config"],"producao":["dashboard","receitas","producao","mapa"],"expedicao":["dashboard","expedicao","pedidos","estoque"],"financeiro":["dashboard","financeiro","relatorios"],"qualidade":["dashboard","producao","estoque","mapa","desempenho","relatorios"]}')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('ALLOW_NEGATIVE_STOCK','0')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('PORTAL_SUPPORT_EMAIL','suporte@indusos.com.br'),
 ('PORTAL_SUPPORT_PHONE','(11) 3000-0000'),
 ('PORTAL_SUPPORT_WHATSAPP','+55 11 99999-9999'),
 ('PORTAL_SUPPORT_HOURS','Segunda a sexta, 8h às 18h'),
 ('PORTAL_SUPPORT_MESSAGE','Fale conosco sempre que precisar ajustar volumes, lotes ou agendar coletas.')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO configuracoes (chave, valor) VALUES
 ('NOTE_FABRICA_NOME','IndusOS Food'),
 ('NOTE_FABRICA_DOC','CNPJ 00.000.000/0000-00'),
 ('NOTE_FABRICA_ENDERECO','Rua Exemplo, 123 - Bairro - Cidade/UF'),
('NOTE_FABRICA_CONTATO','(11) 3000-0000'),
('NOTE_FABRICA_EMAIL','contato@indusos.com.br'),
('NOTE_FABRICA_SITE','indusos.com.br'),
 ('NOTE_LOGO','img/logo.png'),
 ('NOTE_PIX_ENABLED','0'),
 ('NOTE_PIX_KEY',''),
 ('NOTE_PIX_KEY_TYPE','cnpj')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO usuarios (nome, email, senha_hash, papel, is_ativo)
VALUES ('Administrador', 'admin@indusos.local', '$2y$12$yIc8niu5dizOwTDYr9mgJeanDmO3Bs0mP5oFXXXR8OmXIu7/t/Lm.', 'admin', 1)
ON DUPLICATE KEY UPDATE email = email;
