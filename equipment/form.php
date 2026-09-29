<?php
require __DIR__ . '/../includes/bootstrap.php';
$pdo = db();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fields = ['property_no', 'equipment_name', 'quantity_available'];
$data = array_fill_keys($fields, '');
$errors = [];

if ($id) {
    $st = $pdo->prepare('SELECT * FROM equipment WHERE equipment_id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('error', 'Equipment not found.'); redirect('equipment/index.php'); }
    foreach ($fields as $f) $data[$f] = $row[$f];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');
    if ($data['property_no'] === '')    $errors[] = 'Property No. is required.';
    if ($data['equipment_name'] === '') $errors[] = 'Equipment name is required.';
    $qty = filter_var($data['quantity_available'], FILTER_VALIDATE_INT);
    if ($qty === false || $qty < 0)     $errors[] = 'Quantity available must be a whole number, 0 or higher.';

    if (!$errors) {
        try {
            if ($id) {
                $st = $pdo->prepare('UPDATE equipment SET property_no = ?, equipment_name = ?, quantity_available = ? WHERE equipment_id = ?');
                $st->execute([$data['property_no'], $data['equipment_name'], $qty, $id]);
                flash('success', 'Equipment updated.');
            } else {
                $st = $pdo->prepare('INSERT INTO equipment (property_no, equipment_name, quantity_available) VALUES (?, ?, ?)');
                $st->execute([$data['property_no'], $data['equipment_name'], $qty]);
                flash('success', 'Equipment added.');
            }
            redirect('equipment/index.php');
        } catch (PDOException $ex) {
            $errors[] = db_error($ex);
        }
    }
}

$pageTitle = $id ? 'Edit Equipment' : 'Add Equipment';
include __DIR__ . '/../includes/header.php';
?>
<h1><?= e($pageTitle) ?></h1><br>
<?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="card">
    <label>Property No.</label>
    <input name="property_no" value="<?= e($data['property_no']) ?>" required>
    <label>Equipment Name</label>
    <input name="equipment_name" value="<?= e($data['equipment_name']) ?>" required>
    <label>Quantity Available</label>
    <input type="number" min="0" name="quantity_available" value="<?= e($data['quantity_available']) ?>" required>
    <div class="form-actions">
        <button class="btn">Save</button>
        <a class="btn gray" href="index.php">Cancel</a>
    </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
