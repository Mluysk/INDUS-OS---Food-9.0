ALTER TABLE clientes
  ADD COLUMN IF NOT EXISTS contato_tipo ENUM('celular','telefone') DEFAULT 'celular' AFTER telefone,
  ADD COLUMN IF NOT EXISTS whatsapp VARCHAR(30) NULL AFTER contato_tipo;
