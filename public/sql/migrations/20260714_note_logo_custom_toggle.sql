INSERT INTO configuracoes (chave, valor) VALUES
    ('NOTE_LOGO_CUSTOM_ENABLED', '0')
ON DUPLICATE KEY UPDATE valor = valor;
