<?php $title = 'Log - ' . basename($repo['name'], '.git') . " - Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>
<?php require APP_ROOT . '/app/views/partials/repo_nav.php'; ?>

<h2>Commits on <?= htmlspecialchars($ref) ?></h2>

<table class="commits log-table">
    <thead>
        <tr>
            <th>Hash</th>
            <th>Subject</th>
            <th>Author</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($commits as $c): ?>
        <tr>
            <td class="hash"><a href="/<?= htmlspecialchars($repo['name']) ?>/commit/<?= $c['hash'] ?>"><?= $c['short_hash'] ?></a></td>
            <td class="subject"><?= htmlspecialchars($c['subject']) ?></td>
            <td class="author"><?= htmlspecialchars($c['author_name']) ?></td>
            <td class="date"><?= date('Y-m-d H:i', $c['date']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>">&larr; Newer</a>
    <?php endif; ?>
    <?php if (count($commits) === $per_page): ?>
    <a href="?page=<?= $page + 1 ?>">Older &rarr;</a>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
