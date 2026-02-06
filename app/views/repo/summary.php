<?php $title = basename($repo['name'], '.git') . " - Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>
<?php require APP_ROOT . '/app/views/partials/repo_nav.php'; ?>

<section class="summary-section">
    <h2>Files</h2>
    <table class="tree">
        <?php foreach ($tree as $entry): ?>
        <tr>
            <td class="mode"><?= $entry['mode'] ?></td>
            <td class="name">
                <?php if ($entry['type'] === 'tree'): ?>
                <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($ref) ?>/<?= htmlspecialchars($entry['name']) ?>"><?= htmlspecialchars($entry['name']) ?>/</a>
                <?php else: ?>
                <a href="/<?= htmlspecialchars($repo['name']) ?>/blob/<?= htmlspecialchars($ref) ?>/<?= htmlspecialchars($entry['name']) ?>"><?= htmlspecialchars($entry['name']) ?></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>

<section class="summary-section">
    <h2>Recent commits</h2>
    <table class="commits">
        <?php foreach ($commits as $c): ?>
        <tr>
            <td class="hash"><a href="/<?= htmlspecialchars($repo['name']) ?>/commit/<?= $c['hash'] ?>"><?= $c['short_hash'] ?></a></td>
            <td class="subject"><?= htmlspecialchars($c['subject']) ?></td>
            <td class="author"><?= htmlspecialchars($c['author_name']) ?></td>
            <td class="date"><?= date('Y-m-d', $c['date']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="/<?= htmlspecialchars($repo['name']) ?>/log/<?= htmlspecialchars($ref) ?>">View all commits</a></p>
</section>

<?php if ($readme): ?>
<section class="summary-section">
    <h2><?= htmlspecialchars($readme['name']) ?></h2>
    <div class="readme">
        <?php if (str_ends_with(strtolower($readme['name']), '.md')): ?>
        <div class="markdown-body"><?= $parsedown->text($readme['content']) ?></div>
        <?php else: ?>
        <pre><?= htmlspecialchars($readme['content']) ?></pre>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
