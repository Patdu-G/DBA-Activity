<?php
try {
    $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=Enrollment_Activity", "postgres", "041205");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to PostgreSQL!<br>";
    foreach (['borrowers', 'equipment', 'borrow_transactions', 'borrow_items'] as $t) {
        $n = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo "$t: $n rows<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}