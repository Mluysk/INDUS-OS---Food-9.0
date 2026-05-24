ALTER TABLE clientes
  ADD COLUMN IF NOT EXISTS portal_palette_hex VARCHAR(7) NULL AFTER estado,
  ADD COLUMN IF NOT EXISTS portal_palette_name VARCHAR(80) NULL AFTER portal_palette_hex,
  ADD COLUMN IF NOT EXISTS portal_cart_hex VARCHAR(7) NULL AFTER portal_palette_name;
