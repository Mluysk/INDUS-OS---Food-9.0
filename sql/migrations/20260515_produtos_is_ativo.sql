ALTER TABLE produtos
  ADD COLUMN is_ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER tabela_nutricional;
