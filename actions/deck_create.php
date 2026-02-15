<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

verify_csrf();

$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name === '' || mb_strlen($name) > 100) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nazwa talii jest wymagana (max 100 znaków).'];
    redirect('../index.php');
}

$stmt = $pdo->prepare('INSERT INTO decks (name, description) VALUES (:name, :description)');
$stmt->execute([
    'name'        => $name,
    'description' => $description ?: null,
]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Talia „' . $name . '" została utworzona!'];
redirect('../index.php');
