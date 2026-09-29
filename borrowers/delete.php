<?php
require __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $st = db()->prepare('DELETE FROM borrowers WHERE borrower_id = ?');
            $st->execute([$id]);
            flash('success', 'Borrower deleted.');
        } catch (PDOException $ex) {
            flash('error', 'Cannot delete borrower. ' . db_error($ex));
        }
    }
}
redirect('borrowers/index.php');
