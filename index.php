<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/header.php';

$stmt = $pdo->query("
    SELECT 
        d.*,
        COUNT(c.id)                                     AS total_cards,
        SUM(CASE WHEN c.box = 5 THEN 1 ELSE 0 END)    AS learned_cards,
        SUM(CASE WHEN c.next_review <= NOW() THEN 1 ELSE 0 END) AS due_cards
    FROM decks d
    LEFT JOIN cards c ON c.deck_id = d.id
    GROUP BY d.id
    ORDER BY d.created_at DESC
");
$decks = $stmt->fetchAll();

if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $alertClass = ($flash['type'] === 'success') ? 'alert-success' : 'alert-error';
    echo '<div class="alert ' . $alertClass . '">' . e($flash['message']) . '</div>';
}
?>

<div class="page-header">
    <h1>📚 Moje talie</h1>
</div>

<div class="dashboard-grid">

    <?php foreach ($decks as $deck):
        $total   = (int) $deck['total_cards'];
        $learned = (int) $deck['learned_cards'];
        $due     = (int) $deck['due_cards'];
        $percent = $total > 0 ? round(($learned / $total) * 100) : 0;
    ?>
    <div class="deck-tile">
        <div class="deck-tile-name"><?= e($deck['name']) ?></div>

        <?php if ($deck['description']): ?>
            <div class="deck-tile-desc"><?= e($deck['description']) ?></div>
        <?php endif; ?>

        <div class="deck-tile-stats">
            <span>🃏 <?= $total ?> kart</span>
            <span>📖 <?= $due ?> do powtórki</span>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $percent ?>%"></div>
        </div>
        <div class="progress-text"><?= $percent ?>% opanowane (pudełko 5)</div>

        <div class="deck-tile-actions">
            <?php if ($total > 0): ?>
                <a href="study.php?deck_id=<?= (int) $deck['id'] ?>" class="btn btn-primary btn-sm">
                    ▶ Ucz się<?php if ($due > 0) echo " ($due)"; ?>
                </a>
            <?php else: ?>
                <button class="btn btn-primary btn-sm" disabled>▶ Ucz się</button>
            <?php endif; ?>

            <a href="deck.php?id=<?= (int) $deck['id'] ?>" class="btn btn-outline btn-sm">✏️ Edytuj</a>

            <details class="delete-confirm">
                <summary class="btn btn-danger btn-sm">🗑️</summary>
                <div class="delete-confirm-popup">
                    <p>Na pewno chcesz usunąć? Tej operacji nie można cofnąć.</p>
                    <form action="actions/deck_delete.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="deck_id" value="<?= (int) $deck['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Tak</button>
                    </form>
                </div>
            </details>
        </div>
    </div>
    <?php endforeach; ?>

    <a href="#modal-add-deck" class="deck-tile-add">
        <div class="add-icon">+</div>
        <span>Dodaj nową talię</span>
    </a>
</div>

<div class="modal-overlay" id="modal-add-deck">
    <a href="#" class="modal-overlay-bg" aria-hidden="true"></a>
    <div class="modal">
        <h2>➕ Nowa talia</h2>
        <form action="actions/deck_create.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="form-group">
                <label for="deck-name">Nazwa talii</label>
                <input type="text" id="deck-name" name="name" class="form-control"
                       placeholder="np. Angielski - słówka B2" required maxlength="100">
            </div>

            <div class="form-group">
                <label for="deck-desc">Opis (opcjonalnie)</label>
                <textarea id="deck-desc" name="description" class="form-control"
                          placeholder="Krótki opis tematyki talii..." rows="3"></textarea>
            </div>

            <div class="modal-actions">
                <a href="#" class="btn btn-outline">Anuluj</a>
                <button type="submit" class="btn btn-primary">Utwórz talię</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
