<?php
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = config();

    // DEMO MODE: uses a local SQLite file with sample data, no PostgreSQL needed.
    if (!empty($c['demo_mode'])) {
        return $pdo = demo_db();
    }

    $dsn = "pgsql:host={$c['host']};port={$c['port']};dbname={$c['dbname']}";
    try {
        $pdo = new PDO($dsn, $c['user'], $c['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $ex) {
        http_response_code(500);
        die('Database connection failed: ' . htmlspecialchars($ex->getMessage()));
    }
    return $pdo;
}

function demo_db(): PDO {
    if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        die('Demo mode needs the pdo_sqlite extension. In php.ini, make sure "extension=pdo_sqlite" has no ; in front, then restart Apache.');
    }
    $file = __DIR__ . '/../demo.sqlite';
    $isNew = !file_exists($file);
    $pdo = new PDO('sqlite:' . $file, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');

    if ($isNew) {
        $pdo->exec("
            CREATE TABLE borrowers (
                borrower_id   INTEGER PRIMARY KEY AUTOINCREMENT,
                borrower_no   TEXT NOT NULL UNIQUE,
                full_name     TEXT NOT NULL,
                borrower_type TEXT NOT NULL
            );
            CREATE TABLE equipment (
                equipment_id       INTEGER PRIMARY KEY AUTOINCREMENT,
                property_no        TEXT NOT NULL UNIQUE,
                equipment_name     TEXT NOT NULL,
                quantity_available INTEGER NOT NULL CHECK (quantity_available >= 0)
            );
            CREATE TABLE borrow_transactions (
                borrow_id   INTEGER PRIMARY KEY AUTOINCREMENT,
                borrower_id INTEGER NOT NULL REFERENCES borrowers(borrower_id),
                borrow_date TEXT NOT NULL,
                due_date    TEXT NOT NULL,
                status      TEXT NOT NULL
            );
            CREATE TABLE borrow_items (
                borrow_item_id INTEGER PRIMARY KEY AUTOINCREMENT,
                borrow_id      INTEGER NOT NULL REFERENCES borrow_transactions(borrow_id),
                equipment_id   INTEGER NOT NULL REFERENCES equipment(equipment_id),
                quantity       INTEGER NOT NULL CHECK (quantity > 0),
                UNIQUE (borrow_id, equipment_id)
            );
            INSERT INTO borrowers (borrower_no, full_name, borrower_type) VALUES
                ('B-001','Maria Santos','Student'),
                ('B-002','John Reyes','Student'),
                ('B-003','Dr. Ana Cruz','Faculty'),
                ('B-004','Paolo Garcia','Staff'),
                ('B-005','Lea Mendoza','Student');
            INSERT INTO equipment (property_no, equipment_name, quantity_available) VALUES
                ('PN-1001','Microscope',5),
                ('PN-1002','Digital Multimeter',8),
                ('PN-1003','Oscilloscope',3),
                ('PN-1004','Bunsen Burner',10),
                ('PN-1005','Centrifuge',2),
                ('PN-1006','Analytical Balance',4),
                ('PN-1007','Soldering Iron',6),
                ('PN-1008','Breadboard Kit',12);
            INSERT INTO borrow_transactions (borrower_id, borrow_date, due_date, status) VALUES
                (1,'2026-09-01','2026-09-08','Returned'),
                (2,'2026-09-10','2026-09-17','Borrowed'),
                (3,'2026-09-15','2026-09-22','Borrowed'),
                (4,'2026-08-20','2026-08-27','Overdue');
            INSERT INTO borrow_items (borrow_id, equipment_id, quantity) VALUES
                (1,1,1),(1,2,2),(2,3,1),(2,8,2),(3,5,1),(3,6,1),(4,4,3),(4,7,1);
        ");
    }
    return $pdo;
}
