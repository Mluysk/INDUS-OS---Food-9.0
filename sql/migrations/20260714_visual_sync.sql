INSERT INTO configuracoes (chave, valor) VALUES
    ('THEME_VISUAL_SYNC', '0')
ON DUPLICATE KEY UPDATE valor = valor;
