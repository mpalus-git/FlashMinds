<?php
require_once __DIR__ . '/config.php';

$deckId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
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

$pageTitle = $deck['name'];

$stmt = $pdo->prepare('SELECT * FROM cards WHERE deck_id = :deck_id ORDER BY created_at DESC');
$stmt->execute(['deck_id' => $deckId]);
$cards = $stmt->fetchAll();

$editCardId = isset($_GET['edit_card']) ? (int) $_GET['edit_card'] : 0;

$totalCards   = count($cards);
$learnedCards = 0;
$dueCards     = 0;
$now          = new DateTime();

foreach ($cards as $card) {
    if ((int) $card['box'] === 5) $learnedCards++;
    if (new DateTime($card['next_review']) <= $now) $dueCards++;
}

require_once __DIR__ . '/header.php';

if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $alertClass = ($flash['type'] === 'success') ? 'alert-success' : 'alert-error';
    echo '<div class="alert ' . $alertClass . '">' . e($flash['message']) . '</div>';
}
?>

<div class="deck-editor-header">
    <div>
        <h1><?= e($deck['name']) ?></h1>
        <?php if ($deck['description']): ?>
            <p style="color: var(--text-muted); margin-top: 0.25rem; font-size: 0.9rem;">
                <?= e($deck['description']) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="deck-editor-meta">
        <span>🃏 <?= $totalCards ?> kart</span>
        <span>📖 <?= $dueCards ?> do powtórki</span>
        <span>✅ <?= $learnedCards ?> opanowanych</span>
    </div>
</div>

<div class="add-card-form">
    <h3>➕ Dodaj nową kartę</h3>
    <form action="actions/card_create.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="deck_id" value="<?= $deckId ?>">

        <div class="add-card-row">
            <div class="form-group">
                <label for="card-front">Przód (pytanie)</label>
                <input type="text" id="card-front" name="front" class="form-control"
                       placeholder="np. apple" required>
            </div>

            <div class="form-group">
                <label for="card-back">Tył (odpowiedź)</label>
                <input type="text" id="card-back" name="back" class="form-control"
                       placeholder="np. jabłko" required>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Dodaj kartę</button>
            </div>
        </div>
    </form>
</div>

<div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <?php if ($totalCards > 0): ?>
        <a href="study.php?deck_id=<?= $deckId ?>" class="btn btn-success">
            ▶ Ucz się<?php if ($dueCards > 0) echo " ($dueCards do powtórki)"; ?>
        </a>
    <?php endif; ?>
    <a href="index.php" class="btn btn-outline">← Powrót do dashboard</a>
</div>

<?php if ($totalCards === 0): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>Talia jest pusta</h3>
        <p>Dodaj pierwszą kartę używając formularza powyżej.</p>
    </div>
<?php else: ?>
    <div class="cards-table-wrapper">
        <table class="cards-table">
            <thead>
                <tr>
                    <th>Przód (pytanie)</th>
                    <th>Tył (odpowiedź)</th>
                    <th>Pudełko</th>
                    <th>Następna powtórka</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cards as $card): ?>
                <tr>
                    <?php if ($editCardId === (int) $card['id']): ?>
                    <td colspan="2">
                        <form action="actions/card_update.php" method="POST" class="inline-edit-form">
                            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="card_id" value="<?= (int) $card['id'] ?>">
                            <input type="hidden" name="deck_id" value="<?= $deckId ?>">
                            <input type="text" name="front" class="form-control"
                                   value="<?= e($card['front']) ?>" required>
                            <input type="text" name="back" class="form-control"
                                   value="<?= e($card['back']) ?>" required>
                            <button type="submit" class="btn btn-success btn-sm">💾</button>
                            <a href="deck.php?id=<?= $deckId ?>" class="btn btn-danger btn-sm">✖</a>
                        </form>
                    </td>
                    <?php else: ?>
                    <td>
                        <?= e($card['front']) ?>
                    </td>
                    <td>
                        <?= e($card['back']) ?>
                    </td>
                    <?php endif; ?>

                    <td>
                        <span class="box-badge box-<?= (int) $card['box'] ?>">
                            <?= (int) $card['box'] ?>
                        </span>
                    </td>

                    <td>
                        <?php
                        $reviewDate = new DateTime($card['next_review']);
                        $isPast = $reviewDate <= $now;
                        ?>
                        <span style="color: <?= $isPast ? 'var(--danger)' : 'var(--text-muted)' ?>">
                            <?= $isPast ? 'Teraz!' : $reviewDate->format('d.m.Y H:i') ?>
                        </span>
                    </td>

                    <td>
                        <div class="cell-actions">
                            <a href="deck.php?id=<?= $deckId ?>&amp;edit_card=<?= (int) $card['id'] ?>" class="btn btn-outline btn-sm">✏️</a>

                            <details class="delete-confirm">
                                <summary class="btn btn-danger btn-sm">🗑️</summary>
                                <div class="delete-confirm-popup">
                                    <p>Na pewno chcesz usunąć?</p>
                                    <form action="actions/card_delete.php" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="card_id" value="<?= (int) $card['id'] ?>">
                                        <input type="hidden" name="deck_id" value="<?= $deckId ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Tak</button>
                                    </form>
                                </div>
                            </details>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
