<?php $title = "Tony's Git, btw."; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>

<h1>Repositories</h1>

<?php foreach ($repos as $category => $categoryRepos): ?>
<?php if (empty($categoryRepos)) continue; ?>
<h2><?= htmlspecialchars($category) ?></h2>
<table class="repo-list">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Last commit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categoryRepos as $r): ?>
        <tr>
            <td><a href="/<?= htmlspecialchars($r['name']) ?>"><?= htmlspecialchars(basename($r['name'], '.git')) ?></a></td>
            <td><?= htmlspecialchars($r['description']) ?></td>
            <td><?= $r['last_commit'] ? date('Y-m-d H:i', $r['last_commit']) : '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endforeach; ?>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
