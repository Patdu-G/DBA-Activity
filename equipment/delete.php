<?php
require __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $st = db()->prepare('DELETE FROM equipment WHERE equipment_id = ?');
            $st->execute([$id]);
            flash('success', 'Equipment deleted.');
        } catch (PDOException $ex) {
            flash('error', 'Cannot delete equipment. ' . db_error($ex));
        }
    }
}
redirect('equipment/index.php');
