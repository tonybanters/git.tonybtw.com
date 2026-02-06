<?php $title = 'Refs - ' . basename($repo['name'], '.git') . " - Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>
<?php require APP_ROOT . '/app/views/partials/repo_nav.php'; ?>

<section class="refs-section">
    <h2>Branches</h2>
    <?php if ($refs['branches']): ?>
    <ul class="refs-list">
        <?php foreach ($refs['branches'] as $branch): ?>
        <li>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($branch) ?>"><?= htmlspecialchars($branch) ?></a>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/log/<?= htmlspecialchars($branch) ?>" class="ref-link">log</a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p>No branches</p>
    <?php endif; ?>
</section>

<section class="refs-section">
    <h2>Tags</h2>
    <?php if ($refs['tags']): ?>
    <ul class="refs-list">
        <?php foreach ($refs['tags'] as $tag): ?>
        <li>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($tag) ?>"><?= htmlspecialchars($tag) ?></a>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/log/<?= htmlspecialchars($tag) ?>" class="ref-link">log</a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p>No tags</p>
    <?php endif; ?>
</section>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
