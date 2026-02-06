<?php $title = basename($path) . ' - ' . basename($repo['name'], '.git') . " - Tony's Git"; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>
<?php require APP_ROOT . '/app/views/partials/repo_nav.php'; ?>

<?php
$path_parts = explode('/', $path);
$filename = array_pop($path_parts);
$ext = pathinfo($filename, PATHINFO_EXTENSION);

$lang_map = [
    'php' => 'php',
    'js' => 'javascript',
    'ts' => 'typescript',
    'tsx' => 'typescript',
    'jsx' => 'javascript',
    'py' => 'python',
    'rb' => 'ruby',
    'go' => 'go',
    'rs' => 'rust',
    'c' => 'c',
    'h' => 'c',
    'cpp' => 'cpp',
    'hpp' => 'cpp',
    'java' => 'java',
    'sh' => 'bash',
    'bash' => 'bash',
    'zsh' => 'bash',
    'json' => 'json',
    'yaml' => 'yaml',
    'yml' => 'yaml',
    'toml' => 'toml',
    'xml' => 'xml',
    'html' => 'html',
    'css' => 'css',
    'scss' => 'scss',
    'sql' => 'sql',
    'md' => 'markdown',
    'nix' => 'nix',
    'zig' => 'zig',
    'lua' => 'lua',
    'vim' => 'vim',
    'dockerfile' => 'dockerfile',
    'makefile' => 'makefile',
];
$lang = $lang_map[strtolower($ext)] ?? '';

$is_binary = preg_match('/[\x00-\x08\x0E-\x1F]/', substr($content, 0, 8192));
$is_markdown = strtolower($ext) === 'md';
$lines = $is_binary ? [] : explode("\n", $content);
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
    / <?= htmlspecialchars($filename) ?>
</div>

<div class="blob-meta">
    <span><?= number_format($size) ?> bytes</span>
    <a href="/<?= htmlspecialchars($repo['name']) ?>/raw/<?= htmlspecialchars($ref) ?>/<?= htmlspecialchars($path) ?>">raw</a>
</div>

<?php if ($is_binary): ?>
<div class="binary-notice">
    Binary file not shown. <a href="/<?= htmlspecialchars($repo['name']) ?>/raw/<?= htmlspecialchars($ref) ?>/<?= htmlspecialchars($path) ?>">Download</a>
</div>
<?php elseif ($is_markdown): ?>
<div class="readme">
    <div class="markdown-body"><?= $parsedown->text($content) ?></div>
</div>
<?php else: ?>
<div class="blob-content">
    <table class="code">
        <tbody>
            <?php foreach ($lines as $i => $line): ?>
            <tr id="L<?= $i + 1 ?>">
                <td class="line-num"><a href="#L<?= $i + 1 ?>"><?= $i + 1 ?></a></td>
                <td class="line-code"><pre><code class="<?= $lang ? "language-$lang" : '' ?>"><?= htmlspecialchars($line) ?></code></pre></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
