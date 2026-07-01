-- SQL para crear la tabla regulations en SQLite
-- Ejecuta este archivo si el comando 'php artisan migrate' no funciona

CREATE TABLE IF NOT EXISTS regulations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(10) DEFAULT 'pdf',
    content TEXT,
    is_active INTEGER DEFAULT 1,
    created_by INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertar registro en la tabla migrations
INSERT INTO migrations (migration, batch)
VALUES ('2026_06_23_000000_create_regulations_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations));
