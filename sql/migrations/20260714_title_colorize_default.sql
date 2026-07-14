INSERT INTO configuracoes (chave, valor) VALUES
    ('THEME_TITLE_COLORIZE', '1')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

UPDATE configuracoes
SET valor = '1'
WHERE chave = 'THEME_TITLE_COLORIZE'
   OR chave LIKE 'USER%THEME_TITLE_COLORIZE';
