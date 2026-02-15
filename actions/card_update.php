<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

verify_csrf();

$cardId = (int) ($_POST['card_id'] ?? 0);
$deckId = (int) ($_POST['deck_id'] ?? 0);
$front  = trim($_POST['front'] ?? '');
$back   = trim($_POST['back'] ?? '');

if ($cardId <= 0 || $deckId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nieprawidłowe parametry.'];
    redirect('../index.php');
}

if ($front === '' || $back === '') {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Przód i tył karty są wymagane.'];
    redirect("../deck.php?id=$deckId");
}

$stmt = $pdo->prepare('UPDATE cards SET front = :front, back = :back WHERE id = :id');
$stmt->execute([
    'front' => $front,
    'back'  => $back,
    'id'    => $cardId,
]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Karta została zaktualizowana.'];
redirect("../deck.php?id=$deckId");
