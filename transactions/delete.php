<?php
require __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $st = db()->prepare('DELETE FROM borrow_transactions WHERE borrow_id = ?');
            $st->execute([$id]);
            flash('success', 'Transaction deleted.');
        } catch (PDOException $ex) {
            flash('error', 'Cannot delete transaction (delete its borrow items first). ' . db_error($ex));
        }
    }
}
redirect('transactions/index.php');
