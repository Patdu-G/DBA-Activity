<?php
require __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Borrowers';
$rows = db()->query('SELECT * FROM borrowers ORDER BY borrower_id')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="toolbar"><h1>Borrowers</h1><a class="btn" href="form.php">+ Add Borrower</a></div>
<div class="table-wrap"><table>
    <tr><th>ID</th><th>Borrower No.</th><th>Full Name</th><th>Type</th><th>Actions</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= e($r['borrower_id']) ?></td>
        <td><?= e($r['borrower_no']) ?></td>
        <td><?= e($r['full_name']) ?></td>
        <td><?= e($r['borrower_type']) ?></td>
        <td class="actions">
            <a class="btn gray" href="form.php?id=<?= (int)$r['borrower_id'] ?>">Edit</a>
            <form method="post" action="delete.php" onsubmit="return confirm('Delete this borrower?')">
                <input type="hidden" name="id" value="<?= (int)$r['borrower_id'] ?>">
                <button class="btn danger">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; if (!$rows): ?>
    <tr><td colspan="5" class="empty">No borrowers yet.</td></tr>
    <?php endif; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
