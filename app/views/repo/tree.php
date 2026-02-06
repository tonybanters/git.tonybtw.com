<?php $title = basename($repo['name'], '.git') . " - Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>
<?php require APP_ROOT . '/app/views/partials/repo_nav.php'; ?>

<?php
$path_parts = $path ? explode('/', $path) : [];
$breadcrumb = [];
?>

<div class="breadcrumb">
    <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($ref) ?>"><?= htmlspecialchars(basename($repo['name'], '.git')) ?></a>
    <?php
    $accumulated = '';
    foreach ($path_parts as $part):
        $accumulated .= '/' . $part;
    ?>
    / <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($ref) ?><?= htmlspecialchars($accumulated) ?>"><?= htmlspecialchars($part) ?></a>
    <?php endforeach; ?>
</div>

<table class="tree">
    <?php if ($path): ?>
    <tr>
        <td class="mode"></td>
        <td class="name">
            <?php
            $parent = dirname($path);
            $parent_url = $parent === '.' ? '' : '/' . $parent;
            ?>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($ref) ?><?= htmlspecialchars($parent_url) ?>">..</a>
        </td>
    </tr>
    <?php endif; ?>
    <?php foreach ($tree as $entry): ?>
    <tr>
        <td class="mode"><?= $entry['mode'] ?></td>
        <td class="name">
            <?php $entry_path = $path ? "$path/{$entry['name']}" : $entry['name']; ?>
            <?php if ($entry['type'] === 'tree'): ?>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/tree/<?= htmlspecialchars($ref) ?>/<?= htmlspecialchars($entry_path) ?>"><?= htmlspecialchars($entry['name']) ?>/</a>
            <?php else: ?>
            <a href="/<?= htmlspecialchars($repo['name']) ?>/blob/<?= htmlspecialchars($ref) ?>/<?= htmlspecialchars($entry_path) ?>"><?= htmlspecialchars($entry['name']) ?></a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
