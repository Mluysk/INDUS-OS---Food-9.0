ALTER TABLE financeiro_lancamentos
  ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'aberto' AFTER data_entrada;
