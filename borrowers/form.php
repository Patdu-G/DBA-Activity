<?php
require __DIR__ . '/../includes/bootstrap.php';
$pdo = db();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fields = ['borrower_no', 'full_name', 'borrower_type'];
$data = array_fill_keys($fields, '');
$errors = [];

if ($id) {
    $st = $pdo->prepare('SELECT * FROM borrowers WHERE borrower_id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('error', 'Borrower not found.'); redirect('borrowers/index.php'); }
    foreach ($fields as $f) $data[$f] = $row[$f];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');
    foreach ($fields as $f) if ($data[$f] === '') $errors[] = ucwords(str_replace('_', ' ', $f)) . ' is required.';

    if (!$errors) {
        try {
            if ($id) {
                $st = $pdo->prepare('UPDATE borrowers SET borrower_no = ?, full_name = ?, borrower_type = ? WHERE borrower_id = ?');
                $st->execute([$data['borrower_no'], $data['full_name'], $data['borrower_type'], $id]);
                flash('success', 'Borrower updated.');
            } else {
                $st = $pdo->prepare('INSERT INTO borrowers (borrower_no, full_name, borrower_type) VALUES (?, ?, ?)');
                $st->execute([$data['borrower_no'], $data['full_name'], $data['borrower_type']]);
                flash('success', 'Borrower added.');
            }
            redirect('borrowers/index.php');
        } catch (PDOException $ex) {
            $errors[] = db_error($ex);
        }
    }
}

$pageTitle = $id ? 'Edit Borrower' : 'Add Borrower';
include __DIR__ . '/../includes/header.php';
?>
<h1><?= e($pageTitle) ?></h1><br>
<?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="card">
    <label>Borrower No.</label>
    <input name="borrower_no" value="<?= e($data['borrower_no']) ?>" required>
    <label>Full Name</label>
    <input name="full_name" value="<?= e($data['full_name']) ?>" required>
    <label>Borrower Type</label>
    <input name="borrower_type" list="types" value="<?= e($data['borrower_type']) ?>" required>
    <datalist id="types"><option value="Student"><option value="Faculty"><option value="Staff"></datalist>
    <div class="form-actions">
        <button class="btn">Save</button>
        <a class="btn gray" href="index.php">Cancel</a>
    </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
