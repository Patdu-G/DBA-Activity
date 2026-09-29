<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Dashboard';
$pdo = db();
$counts = [
    'Borrowers'    => ['borrowers', 'borrowers/index.php'],
    'Equipment'    => ['equipment', 'equipment/index.php'],
    'Transactions' => ['borrow_transactions', 'transactions/index.php'],
    'Borrow Items' => ['borrow_items', 'items/index.php'],
];
include __DIR__ . '/includes/header.php';
?>
<h1>Campus Laboratory Equipment Borrowing</h1>
<div class="stats">
<?php foreach ($counts as $label => [$table, $link]): ?>
    <a class="stat" href="<?= url($link) ?>">
        <strong><?= (int)$pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn() ?></strong>
        <?= e($label) ?>
    </a>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
