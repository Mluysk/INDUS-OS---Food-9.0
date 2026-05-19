ALTER TABLE recipes
  ADD COLUMN IF NOT EXISTS produto_pa_id INT NULL AFTER sku;

SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'recipes'
      AND CONSTRAINT_NAME = 'fk_recipes_produto_pa'
);

SET @fk_sql := IF(
    @fk_exists = 0,
    'ALTER TABLE recipes ADD CONSTRAINT fk_recipes_produto_pa FOREIGN KEY (produto_pa_id) REFERENCES produtos(id)',
    'SELECT 1'
);
PREPARE stmt FROM @fk_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
