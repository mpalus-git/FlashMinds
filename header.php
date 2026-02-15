<?php
require_once __DIR__ . '/config.php';

$pageTitle = isset($pageTitle) ? e($pageTitle) . ' - FlashMinds' : 'FlashMinds';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="navbar-brand">
            <span class="brand-icon">⚡</span> FlashMinds
        </a>
        <a href="index.php" class="navbar-link">Dashboard</a>
    </div>
</nav>

<main class="container">
