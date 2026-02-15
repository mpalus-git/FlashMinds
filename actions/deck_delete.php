<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

verify_csrf();

$deckId = (int) ($_POST['deck_id'] ?? 0);

if ($deckId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nieprawidłowy identyfikator talii.'];
    redirect('../index.php');
}

$stmt = $pdo->prepare('SELECT name FROM decks WHERE id = :id');
$stmt->execute(['id' => $deckId]);
$deck = $stmt->fetch();

if (!$deck) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Talia nie istnieje.'];
    redirect('../index.php');
}

$stmt = $pdo->prepare('DELETE FROM decks WHERE id = :id');
$stmt->execute(['id' => $deckId]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Talia „' . $deck['name'] . '" została usunięta.'];
redirect('../index.php');
