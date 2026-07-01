-- SQL para crear la tabla vacations en SQLite
-- Ejecuta este archivo si el comando 'php artisan migrate' no funciona

CREATE TABLE IF NOT EXISTS vacations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    company_id INTEGER NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days_taken INTEGER NOT NULL,
    year INTEGER NOT NULL,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'approved' CHECK(status IN ('pending', 'approved', 'rejected', 'completed')),
    approved_by INTEGER,
    approved_at DATETIME,
    created_by INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_vacations_employee_year ON vacations(employee_id, year);
CREATE INDEX IF NOT EXISTS idx_vacations_company_year ON vacations(company_id, year);

-- Insertar registro en la tabla migrations
INSERT INTO migrations (migration, batch)
VALUES ('2026_06_23_100000_create_vacations_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations));
