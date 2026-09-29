<?php
require __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Borrow Items';
$rows = db()->query(
    'SELECT i.borrow_item_id, i.borrow_id, i.quantity, b.full_name, e.equipment_name, e.property_no
     FROM borrow_items i
     JOIN borrow_transactions t ON t.borrow_id = i.borrow_id
     JOIN borrowers b ON b.borrower_id = t.borrower_id
     JOIN equipment e ON e.equipment_id = i.equipment_id
     ORDER BY i.borrow_item_id'
)->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="toolbar"><h1>Borrow Items</h1><a class="btn" href="form.php">+ Add Item</a></div>
<div class="table-wrap"><table>
    <tr><th>ID</th><th>Transaction</th><th>Borrower</th><th>Equipment</th><th>Qty</th><th>Actions</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= e($r['borrow_item_id']) ?></td>
        <td>#<?= e($r['borrow_id']) ?></td>
        <td><?= e($r['full_name']) ?></td>
        <td><?= e($r['equipment_name']) ?> (<?= e($r['property_no']) ?>)</td>
        <td><?= e($r['quantity']) ?></td>
        <td class="actions">
            <a class="btn gray" href="form.php?id=<?= (int)$r['borrow_item_id'] ?>">Edit</a>
            <form method="post" action="delete.php" onsubmit="return confirm('Delete this item?')">
                <input type="hidden" name="id" value="<?= (int)$r['borrow_item_id'] ?>">
                <button class="btn danger">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; if (!$rows): ?>
    <tr><td colspan="6" class="empty">No borrow items yet.</td></tr>
    <?php endif; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
