INSERT INTO configuracoes (chave, valor)
VALUES ('PORTAL_CATALOG_PAGE_SIZE', '30')
ON DUPLICATE KEY UPDATE valor = IF(valor = '15', VALUES(valor), valor);
