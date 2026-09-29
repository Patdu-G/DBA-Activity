<?php
require __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Equipment';
$rows = db()->query('SELECT * FROM equipment ORDER BY equipment_id')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="toolbar"><h1>Equipment</h1><a class="btn" href="form.php">+ Add Equipment</a></div>
<div class="table-wrap"><table>
    <tr><th>ID</th><th>Property No.</th><th>Equipment Name</th><th>Qty Available</th><th>Actions</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= e($r['equipment_id']) ?></td>
        <td><?= e($r['property_no']) ?></td>
        <td><?= e($r['equipment_name']) ?></td>
        <td><?= e($r['quantity_available']) ?></td>
        <td class="actions">
            <a class="btn gray" href="form.php?id=<?= (int)$r['equipment_id'] ?>">Edit</a>
            <form method="post" action="delete.php" onsubmit="return confirm('Delete this equipment?')">
                <input type="hidden" name="id" value="<?= (int)$r['equipment_id'] ?>">
                <button class="btn danger">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; if (!$rows): ?>
    <tr><td colspan="5" class="empty">No equipment yet.</td></tr>
    <?php endif; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
