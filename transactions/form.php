<?php
require __DIR__ . '/../includes/bootstrap.php';
$pdo = db();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fields = ['borrower_id', 'borrow_date', 'due_date', 'status'];
$data = array_fill_keys($fields, '');
$data['borrow_date'] = date('Y-m-d');
$data['status'] = 'Borrowed';
$errors = [];
$statuses = ['Borrowed', 'Returned', 'Overdue'];
$borrowers = $pdo->query('SELECT borrower_id, borrower_no, full_name FROM borrowers ORDER BY full_name')->fetchAll();

if ($id) {
    $st = $pdo->prepare('SELECT * FROM borrow_transactions WHERE borrow_id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('error', 'Transaction not found.'); redirect('transactions/index.php'); }
    foreach ($fields as $f) $data[$f] = $row[$f];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');
    foreach ($fields as $f) if ($data[$f] === '') $errors[] = ucwords(str_replace('_', ' ', $f)) . ' is required.';
    if ($data['borrow_date'] && $data['due_date'] && $data['due_date'] < $data['borrow_date'])
        $errors[] = 'Due date cannot be earlier than the borrow date.';

    if (!$errors) {
        try {
            $params = [(int)$data['borrower_id'], $data['borrow_date'], $data['due_date'], $data['status']];
            if ($id) {
                $st = $pdo->prepare('UPDATE borrow_transactions SET borrower_id = ?, borrow_date = ?, due_date = ?, status = ? WHERE borrow_id = ?');
                $st->execute([...$params, $id]);
                flash('success', 'Transaction updated.');
            } else {
                $st = $pdo->prepare('INSERT INTO borrow_transactions (borrower_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?)');
                $st->execute($params);
                flash('success', 'Transaction added.');
            }
            redirect('transactions/index.php');
        } catch (PDOException $ex) {
            $errors[] = db_error($ex);
        }
    }
}

$pageTitle = $id ? 'Edit Transaction' : 'Add Transaction';
include __DIR__ . '/../includes/header.php';
?>
<h1><?= e($pageTitle) ?></h1><br>
<?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="card">
    <label>Borrower</label>
    <select name="borrower_id" required>
        <option value="">-- select borrower --</option>
        <?php foreach ($borrowers as $b): ?>
        <option value="<?= (int)$b['borrower_id'] ?>" <?= (string)$data['borrower_id'] === (string)$b['borrower_id'] ? 'selected' : '' ?>>
            <?= e($b['full_name']) ?> (<?= e($b['borrower_no']) ?>)
        </option>
        <?php endforeach; ?>
    </select>
    <label>Borrow Date</label>
    <input type="date" name="borrow_date" value="<?= e($data['borrow_date']) ?>" required>
    <label>Due Date</label>
    <input type="date" name="due_date" value="<?= e($data['due_date']) ?>" required>
    <label>Status</label>
    <select name="status" required>
        <?php foreach ($statuses as $s): ?>
        <option <?= $data['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <div class="form-actions">
        <button class="btn">Save</button>
        <a class="btn gray" href="index.php">Cancel</a>
    </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
