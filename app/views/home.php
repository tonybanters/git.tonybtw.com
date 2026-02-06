<?php $title = "Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>

<h1>Repositories</h1>

<table class="repo-list">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Last commit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($repos as $r): ?>
        <tr>
            <td><a href="/<?= htmlspecialchars($r['name']) ?>"><?= htmlspecialchars(basename($r['name'], '.git')) ?></a></td>
            <td><?= htmlspecialchars($r['description']) ?></td>
            <td><?= $r['last_commit'] ? date('Y-m-d H:i', $r['last_commit']) : '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
