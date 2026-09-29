<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Lab Borrowing') ?> | Lab Borrowing</title>
    <link rel="stylesheet" href="<?= url('assets/style.css') ?>">
</head>
<body>
<nav>
    <a class="brand" href="<?= url('index.php') ?>">Lab Borrowing</a>
    <a href="<?= url('borrowers/index.php') ?>">Borrowers</a>
    <a href="<?= url('equipment/index.php') ?>">Equipment</a>
    <a href="<?= url('transactions/index.php') ?>">Transactions</a>
    <a href="<?= url('items/index.php') ?>">Borrow Items</a>
    <a href="<?= url('report.php') ?>">Report</a>
</nav>
<?php if (!empty(config()['demo_mode'])): ?>
<div class="demo-banner">DEMO MODE: showing sample data from a local file, not the real database.</div>
<?php endif; ?>
<main>
<?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="alert <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
