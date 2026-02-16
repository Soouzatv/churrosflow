ALTER TABLE restaurants
    ADD COLUMN logo_path VARCHAR(255) NULL AFTER slug,
    ADD COLUMN primary_color CHAR(7) NOT NULL DEFAULT '#FF7A00' AFTER logo_path,
    ADD COLUMN primary_color_2 CHAR(7) NOT NULL DEFAULT '#FF9F45' AFTER primary_color,
    ADD COLUMN sidebar_color_a CHAR(7) NOT NULL DEFAULT '#050505' AFTER primary_color_2,
    ADD COLUMN sidebar_color_b CHAR(7) NOT NULL DEFAULT '#151515' AFTER sidebar_color_a;
