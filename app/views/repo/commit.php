<?php $title = $commit['short_hash'] . ' - ' . basename($repo['name'], '.git') . " - Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>
<?php require APP_ROOT . '/app/views/partials/repo_nav.php'; ?>

<div class="commit-detail">
    <h2><?= htmlspecialchars($commit['subject']) ?></h2>

    <dl class="commit-meta">
        <dt>Commit</dt>
        <dd><code><?= $commit['hash'] ?></code></dd>

        <?php if ($commit['parents']): ?>
        <dt>Parent<?= count($commit['parents']) > 1 ? 's' : '' ?></dt>
        <dd>
            <?php foreach ($commit['parents'] as $parent): ?>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/commit/<?= $parent ?>"><code><?= substr($parent, 0, 7) ?></code></a>
            <?php endforeach; ?>
        </dd>
        <?php endif; ?>

        <dt>Author</dt>
        <dd><?= htmlspecialchars($commit['author_name']) ?> &lt;<?= htmlspecialchars($commit['author_email']) ?>&gt;</dd>

        <dt>Date</dt>
        <dd><?= date('Y-m-d H:i:s', $commit['date']) ?></dd>
    </dl>

    <?php if ($commit['body']): ?>
    <pre class="commit-body"><?= htmlspecialchars($commit['body']) ?></pre>
    <?php endif; ?>
</div>

<div class="diff-container">
    <h3>Diff</h3>
    <pre class="diff"><code class="language-diff"><?= htmlspecialchars($diff) ?></code></pre>
</div>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
