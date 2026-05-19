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
