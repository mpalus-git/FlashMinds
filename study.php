<?php
require_once __DIR__ . '/config.php';

$deckId = isset($_GET['deck_id']) ? (int) $_GET['deck_id'] : 0;
if ($deckId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM decks WHERE id = :id');
$stmt->execute(['id' => $deckId]);
$deck = $stmt->fetch();

if (!$deck) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Talia nie istnieje.'];
    header('Location: index.php');
    exit;
}

$pageTitle = 'Nauka: ' . $deck['name'];

$stmtCount = $pdo->prepare('
    SELECT COUNT(*) AS due_count 
    FROM cards 
    WHERE deck_id = :deck_id AND next_review <= NOW()
');
$stmtCount->execute(['deck_id' => $deckId]);
$dueCount = (int) $stmtCount->fetch()['due_count'];

$card = null;
if ($dueCount > 0) {
    $stmtCard = $pdo->prepare('
        SELECT * FROM cards 
        WHERE deck_id = :deck_id AND next_review <= NOW()
        ORDER BY box ASC, RAND()
        LIMIT 1
    ');
    $stmtCard->execute(['deck_id' => $deckId]);
    $card = $stmtCard->fetch();
}

$stmtTotal = $pdo->prepare('SELECT COUNT(*) AS cnt FROM cards WHERE deck_id = :deck_id');
$stmtTotal->execute(['deck_id' => $deckId]);
$totalCards = (int) $stmtTotal->fetch()['cnt'];

require_once __DIR__ . '/header.php';
?>

<div class="study-wrapper">

    <div class="study-info">
        <strong><?= e($deck['name']) ?></strong><br>
        <?php if ($card): ?>
            Pozostało kart do powtórki: <strong><?= $dueCount ?></strong> / <?= $totalCards ?>
        <?php endif; ?>
    </div>

    <?php if ($totalCards === 0): ?>
        <div class="study-complete">
            <div class="complete-icon">📭</div>
            <h2>Talia jest pusta</h2>
            <p>Dodaj karty, aby rozpocząć naukę.</p>
            <a href="deck.php?id=<?= $deckId ?>" class="btn btn-primary btn-lg">
                ✏️ Przejdź do edytora talii
            </a>
        </div>

    <?php elseif (!$card): ?>
        <div class="study-complete">
            <div class="complete-icon">🎉</div>
            <h2>Wszystko powtórzone!</h2>
            <p>Nie masz teraz kart do powtórki. Wróć później.</p>
            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <a href="index.php" class="btn btn-primary btn-lg">← Dashboard</a>
                <a href="deck.php?id=<?= $deckId ?>" class="btn btn-outline btn-lg">✏️ Edytuj talię</a>
            </div>
        </div>

    <?php else: ?>
        <input type="checkbox" id="flip-toggle" class="flip-checkbox">
        <label for="flip-toggle" class="card-container" title="Kliknij, aby odwrócić kartę">
            <div class="card-inner">
                <div class="card-front">
                    <?= e($card['front']) ?>
                </div>
                <div class="card-back">
                    <?= e($card['back']) ?>
                </div>
            </div>
        </label>

        <p class="card-hint">👆 Kliknij kartę, aby zobaczyć odpowiedź</p>

        <div class="study-actions">
            <form action="actions/study_answer.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="card_id" value="<?= (int) $card['id'] ?>">
                <input type="hidden" name="deck_id" value="<?= $deckId ?>">
                <input type="hidden" name="answer" value="dont_know">
                <button type="submit" class="btn-dont-know">
                    ✗ Nie umiem
                </button>
            </form>

            <form action="actions/study_answer.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="card_id" value="<?= (int) $card['id'] ?>">
                <input type="hidden" name="deck_id" value="<?= $deckId ?>">
                <input type="hidden" name="answer" value="know">
                <button type="submit" class="btn-know">
                    ✓ Umiem
                </button>
            </form>
        </div>

        <div class="study-info" style="margin-top: 0.5rem;">
            Pudełko: <span class="box-badge box-<?= (int) $card['box'] ?>"><?= (int) $card['box'] ?></span>
        </div>
    <?php endif; ?>

    <?php if ($card): ?>
        <div>
            <a href="index.php" class="btn btn-outline" style="margin-top: 1rem;">← Wróć do dashboard</a>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
