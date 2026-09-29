<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Report';
$rows = db()->query(
    'SELECT b.borrower_no, b.full_name, t.borrow_id, t.borrow_date, t.due_date,
            e.property_no, e.equipment_name, i.quantity, t.status
     FROM borrow_items i
     JOIN borrow_transactions t ON t.borrow_id = i.borrow_id
     JOIN borrowers b ON b.borrower_id = t.borrower_id
     JOIN equipment e ON e.equipment_id = i.equipment_id
     ORDER BY t.borrow_date DESC, b.full_name'
)->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="toolbar"><h1>Borrowing Report</h1></div>
<div class="table-wrap"><table>
    <tr><th>Borrower</th><th>Transaction Date</th><th>Due Date</th><th>Equipment</th><th>Qty</th><th>Status</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= e($r['full_name']) ?> (<?= e($r['borrower_no']) ?>)</td>
        <td><?= e($r['borrow_date']) ?></td>
        <td><?= e($r['due_date']) ?></td>
        <td><?= e($r['equipment_name']) ?> (<?= e($r['property_no']) ?>)</td>
        <td><?= e($r['quantity']) ?></td>
        <td><?= e($r['status']) ?></td>
    </tr>
    <?php endforeach; if (!$rows): ?>
    <tr><td colspan="6" class="empty">No records yet.</td></tr>
    <?php endif; ?>
</table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
