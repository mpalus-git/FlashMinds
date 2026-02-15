<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

verify_csrf();

$cardId = (int) ($_POST['card_id'] ?? 0);
$deckId = (int) ($_POST['deck_id'] ?? 0);
$answer = $_POST['answer'] ?? '';

if ($cardId <= 0 || $deckId <= 0 || !in_array($answer, ['know', 'dont_know'], true)) {
    redirect("../study.php?deck_id=$deckId");
}

$stmt = $pdo->prepare('SELECT box FROM cards WHERE id = :id');
$stmt->execute(['id' => $cardId]);
$card = $stmt->fetch();

if (!$card) {
    redirect("../study.php?deck_id=$deckId");
}

$currentBox = (int) $card['box'];

if ($answer === 'know') {
    $newBox = min($currentBox + 1, 5);
} else {
    $newBox = 1;
}

$intervalDays = leitner_interval($newBox);

$stmt = $pdo->prepare('
    UPDATE cards 
    SET box = :box, 
        next_review = DATE_ADD(NOW(), INTERVAL :days DAY)
    WHERE id = :id
');
$stmt->execute([
    'box'  => $newBox,
    'days' => $intervalDays,
    'id'   => $cardId,
]);

redirect("../study.php?deck_id=$deckId");
