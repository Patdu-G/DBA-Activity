<?php
require __DIR__ . '/../includes/bootstrap.php';
$pdo = db();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fields = ['borrow_id', 'equipment_id', 'quantity'];
$data = array_fill_keys($fields, '');
$errors = [];

$txns = $pdo->query(
    'SELECT t.borrow_id, t.borrow_date, b.full_name FROM borrow_transactions t
     JOIN borrowers b ON b.borrower_id = t.borrower_id ORDER BY t.borrow_id'
)->fetchAll();
$equip = $pdo->query('SELECT equipment_id, property_no, equipment_name FROM equipment ORDER BY equipment_name')->fetchAll();

if ($id) {
    $st = $pdo->prepare('SELECT * FROM borrow_items WHERE borrow_item_id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('error', 'Item not found.'); redirect('items/index.php'); }
    foreach ($fields as $f) $data[$f] = $row[$f];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');
    if ($data['borrow_id'] === '')    $errors[] = 'Transaction is required.';
    if ($data['equipment_id'] === '') $errors[] = 'Equipment is required.';
    $qty = filter_var($data['quantity'], FILTER_VALIDATE_INT);
    if ($qty === false || $qty < 1)   $errors[] = 'Quantity must be a whole number of at least 1.';

    if (!$errors) {
        try {
            $params = [(int)$data['borrow_id'], (int)$data['equipment_id'], $qty];
            if ($id) {
                $st = $pdo->prepare('UPDATE borrow_items SET borrow_id = ?, equipment_id = ?, quantity = ? WHERE borrow_item_id = ?');
                $st->execute([...$params, $id]);
                flash('success', 'Borrow item updated.');
            } else {
                $st = $pdo->prepare('INSERT INTO borrow_items (borrow_id, equipment_id, quantity) VALUES (?, ?, ?)');
                $st->execute($params);
                flash('success', 'Borrow item added.');
            }
            redirect('items/index.php');
        } catch (PDOException $ex) {
            $errors[] = db_error($ex);
        }
    }
}

$pageTitle = $id ? 'Edit Borrow Item' : 'Add Borrow Item';
include __DIR__ . '/../includes/header.php';
?>
<h1><?= e($pageTitle) ?></h1><br>
<?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="card">
    <label>Transaction</label>
    <select name="borrow_id" required>
        <option value="">-- select transaction --</option>
        <?php foreach ($txns as $t): ?>
        <option value="<?= (int)$t['borrow_id'] ?>" <?= (string)$data['borrow_id'] === (string)$t['borrow_id'] ? 'selected' : '' ?>>
            #<?= e($t['borrow_id']) ?> - <?= e($t['full_name']) ?> (<?= e($t['borrow_date']) ?>)
        </option>
        <?php endforeach; ?>
    </select>
    <label>Equipment</label>
    <select name="equipment_id" required>
        <option value="">-- select equipment --</option>
        <?php foreach ($equip as $q): ?>
        <option value="<?= (int)$q['equipment_id'] ?>" <?= (string)$data['equipment_id'] === (string)$q['equipment_id'] ? 'selected' : '' ?>>
            <?= e($q['equipment_name']) ?> (<?= e($q['property_no']) ?>)
        </option>
        <?php endforeach; ?>
    </select>
    <label>Quantity</label>
    <input type="number" min="1" name="quantity" value="<?= e($data['quantity']) ?>" required>
    <div class="form-actions">
        <button class="btn">Save</button>
        <a class="btn gray" href="index.php">Cancel</a>
    </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
