<?php
require __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Transactions';
$rows = db()->query(
    'SELECT t.*, b.full_name FROM borrow_transactions t
     JOIN borrowers b ON b.borrower_id = t.borrower_id
     ORDER BY t.borrow_id'
)->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="toolbar"><h1>Borrow Transactions</h1><a class="btn" href="form.php">+ Add Transaction</a></div>
<div class="table-wrap"><table>
    <tr><th>ID</th><th>Borrower</th><th>Borrow Date</th><th>Due Date</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= e($r['borrow_id']) ?></td>
        <td><?= e($r['full_name']) ?></td>
        <td><?= e($r['borrow_date']) ?></td>
        <td><?= e($r['due_date']) ?></td>
        <td><?= e($r['status']) ?></td>
        <td class="actions">
            <a class="btn gray" href="form.php?id=<?= (int)$r['borrow_id'] ?>">Edit</a>
            <form method="post" action="delete.php" onsubmit="return confirm('Delete this transaction?')">
                <input type="hidden" name="id" value="<?= (int)$r['borrow_id'] ?>">
                <button class="btn danger">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; if (!$rows): ?>
    <tr><td colspan="6" class="empty">No transactions yet.</td></tr>
    <?php endif; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
