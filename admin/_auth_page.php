<?php
/** Kerangka halaman masuk / pemasangan (tanpa sidebar). Variabel: $title, $err, $intro. */
function auth_foot()
{
    echo '</div></div></body></html>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> | Admin AgroNusa</title>
<link rel="icon" href="../assets/images/logo-icon.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css?v=<?= filemtime(__DIR__ . '/admin.css') ?>">
</head>
<body class="admin auth">
<div class="auth-wrap"><div class="auth-card">
<img class="auth-logo" src="../assets/images/logo-agronusa.png" alt="AgroNusa" width="102" height="84">
<h1><?= e($title) ?></h1>
<?php if (!empty($intro)) : ?><p class="muted"><?= e($intro) ?></p><?php endif; ?>
<?php if (!empty($err)) : ?><div class="flash err" role="alert"><?= e($err) ?></div><?php endif; ?>
