<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

verify_csrf();

$cardId = (int) ($_POST['card_id'] ?? 0);
$deckId = (int) ($_POST['deck_id'] ?? 0);

if ($cardId <= 0 || $deckId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nieprawidłowe parametry.'];
    redirect('../index.php');
}

$stmt = $pdo->prepare('DELETE FROM cards WHERE id = :id');
$stmt->execute(['id' => $cardId]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Karta została usunięta.'];
redirect("../deck.php?id=$deckId");
