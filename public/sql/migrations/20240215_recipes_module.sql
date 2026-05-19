-- Receita (BOM) module tables and configuration

CREATE TABLE IF NOT EXISTS recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  sku VARCHAR(50) NULL,
  yield_qty DECIMAL(10,3) NOT NULL,
  yield_unit VARCHAR(20) NOT NULL,
  active TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

ALTER TABLE produtos
  ADD COLUMN recipe_id INT NULL AFTER categoria;

ALTER TABLE produtos
  ADD CONSTRAINT fk_produtos_recipe
  FOREIGN KEY (recipe_id) REFERENCES recipes(id);

CREATE UNIQUE INDEX uniq_recipe_run_source ON recipe_runs (source, source_id);

INSERT INTO configuracoes (chave, valor) VALUES
  ('ALLOW_NEGATIVE_STOCK', '0')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);
