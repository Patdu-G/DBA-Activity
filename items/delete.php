<?php
require __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $st = db()->prepare('DELETE FROM borrow_items WHERE borrow_item_id = ?');
            $st->execute([$id]);
            flash('success', 'Borrow item deleted.');
        } catch (PDOException $ex) {
            flash('error', 'Cannot delete item. ' . db_error($ex));
        }
    }
}
redirect('items/index.php');
