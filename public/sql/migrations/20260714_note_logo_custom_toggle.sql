INSERT INTO configuracoes (chave, valor) VALUES
    ('NOTE_LOGO_CUSTOM_ENABLED', '0'),
    ('NOTE_LOGO_SIZE', '66'),
    ('NOTE_LOGO_OFFSET_X', '0'),
    ('NOTE_LOGO_OFFSET_Y', '0')
ON DUPLICATE KEY UPDATE valor = valor;
