<?php $title = 'Error'; ?>
<?php require APP_ROOT . '/app/views/partials/header.php'; ?>

<div class="error">
    <h1><?= htmlspecialchars($error) ?></h1>
    <p><a href="/">Back to repositories</a></p>
</div>

<?php require APP_ROOT . '/app/views/partials/footer.php'; ?>
