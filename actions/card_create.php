<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

verify_csrf();

$deckId = (int) ($_POST['deck_id'] ?? 0);
$front  = trim($_POST['front'] ?? '');
$back   = trim($_POST['back'] ?? '');

if ($deckId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Nieprawidłowy identyfikator talii.'];
    redirect('../index.php');
}

if ($front === '' || $back === '') {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Przód i tył karty są wymagane.'];
    redirect("../deck.php?id=$deckId");
}

$stmt = $pdo->prepare('SELECT id FROM decks WHERE id = :id');
$stmt->execute(['id' => $deckId]);
if (!$stmt->fetch()) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Talia nie istnieje.'];
    redirect('../index.php');
}

$stmt = $pdo->prepare('
    INSERT INTO cards (deck_id, front, back, box, next_review) 
    VALUES (:deck_id, :front, :back, 1, NOW())
');
$stmt->execute([
    'deck_id' => $deckId,
    'front'   => $front,
    'back'    => $back,
]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Karta została dodana.'];
redirect("../deck.php?id=$deckId");
