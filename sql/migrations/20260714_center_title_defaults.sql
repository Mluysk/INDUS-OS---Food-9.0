INSERT INTO configuracoes (chave, valor) VALUES
    ('THEME_TITLE_SIZE', '22'),
    ('THEME_TITLE_OFFSET_X', '0'),
    ('THEME_TITLE_OFFSET_Y', '0')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

UPDATE configuracoes
SET valor = '22'
WHERE chave = 'THEME_TITLE_SIZE'
   OR chave LIKE 'USER\_%\_THEME\_TITLE\_SIZE' ESCAPE '\\';

UPDATE configuracoes
SET valor = '0'
WHERE chave IN ('THEME_TITLE_OFFSET_X', 'THEME_TITLE_OFFSET_Y')
   OR chave LIKE 'USER\_%\_THEME\_TITLE\_OFFSET\_X' ESCAPE '\\'
   OR chave LIKE 'USER\_%\_THEME\_TITLE\_OFFSET\_Y' ESCAPE '\\';
