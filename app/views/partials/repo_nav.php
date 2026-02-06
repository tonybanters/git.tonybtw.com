<div class="repo-header">
    <h1><a href="/<?= htmlspecialchars($repo['name']) ?>"><?= htmlspecialchars(basename($repo['name'], '.git')) ?></a></h1>
    <?php if ($repo['description']): ?>
    <p class="description"><?= htmlspecialchars($repo['description']) ?></p>
    <?php endif; ?>
</div>

<nav class="repo-nav">
    <a href="/<?= htmlspecialchars($repo['name']) ?>">summary</a>
    <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($ref ?? $repo['default_branch']) ?>">tree</a>
    <a href="/<?= htmlspecialchars($repo['name']) ?>/log/<?= htmlspecialchars($ref ?? $repo['default_branch']) ?>">log</a>
    <a href="/<?= htmlspecialchars($repo['name']) ?>/refs">refs</a>
</nav>

<div class="clone-urls">
    <code><?= htmlspecialchars($clone_urls['https']) ?></code>
    <code><?= htmlspecialchars($clone_urls['git']) ?></code>
</div>
