<?php
/** Dasar panel admin: sesi, otentikasi, dan kerangka halaman. */
require_once __DIR__ . '/../includes/functions.php';
start_session();
header('X-Robots-Tag: noindex, nofollow');
header('X-Frame-Options: SAMEORIGIN');
header('Cache-Control: no-store');

function admin_users_exist()
{
    return (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
}

function require_admin()
{
    if (!admin_users_exist()) {
        redirect('setup.php');
    }
    if (empty($_SESSION['uid'])) {
        redirect('login.php');
    }
    if (!empty($_SESSION['last']) && time() - $_SESSION['last'] > 4 * 3600) {
        unset($_SESSION['uid'], $_SESSION['uname']);
        redirect('login.php?exp=1');
    }
    $_SESSION['last'] = time();
}

function admin_login($id, $name)
{
    session_regenerate_id(true);
    $_SESSION['uid'] = (int) $id;
    $_SESSION['uname'] = $name;
    $_SESSION['last'] = time();
}

function flash($msg, $type = 'ok')
{
    $_SESSION['flash'] = array($msg, $type);
}

function unread_count()
{
    return (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
}

function admin_head($title, $active = '')
{
    $nav = array(
        'dashboard' => array('index.php', 'Dashboard'),
        'artikel' => array('artikel.php', 'Artikel'),
        'kategori' => array('kategori.php', 'Kategori'),
        'pesan' => array('pesan.php', 'Pesan masuk'),
        'pengaturan' => array('pengaturan.php', 'Pengaturan'),
    );
    $unread = unread_count();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> | Admin AgroNusa</title>
<link rel="icon" href="../assets/images/logo-icon.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css?v=<?= filemtime(__DIR__ . '/admin.css') ?>">
</head>
<body class="admin">
<div class="shell">
 <aside class="side" id="side">
  <a class="side-brand" href="index.php"><img src="../assets/images/logo-icon.png" alt="" width="34" height="31"><span>AgroNusa <small>Admin</small></span></a>
  <nav class="side-nav" aria-label="Menu admin">
   <?php foreach ($nav as $k => $n) : ?><a href="<?= $n[0] ?>"<?= $active === $k ? ' aria-current="page"' : '' ?>><?= e($n[1]) ?><?php if ($k === 'pesan' && $unread > 0) : ?><b class="badge"><?= $unread ?></b><?php endif; ?></a><?php endforeach; ?>
  </nav>
  <div class="side-foot">
   <a href="../index.php" target="_blank" rel="noopener">Lihat situs ↗</a>
   <form method="post" action="logout.php"><?= csrf_field() ?><button type="submit" class="link">Keluar (<?= e(isset($_SESSION['uname']) ? $_SESSION['uname'] : '') ?>)</button></form>
  </div>
 </aside>
 <div class="main">
  <header class="top"><button type="button" class="burger" id="burger" aria-label="Buka menu" aria-controls="side" aria-expanded="false"><svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button><h1><?= e($title) ?></h1></header>
  <main class="content">
<?php
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="flash ' . ($f[1] === 'err' ? 'err' : 'ok') . '" role="status">' . e($f[0]) . '</div>';
    }
}

function admin_foot()
{
    ?>
  </main>
 </div>
</div>
<script src="admin.js?v=<?= filemtime(__DIR__ . '/admin.js') ?>"></script>
</body>
</html>
<?php
}

/** Tombol hapus kecil (form POST + konfirmasi). */
function delete_form($action, $fields, $label = 'Hapus', $confirm = 'Hapus data ini? Tindakan tidak bisa dibatalkan.')
{
    $h = '<form method="post" action="' . e($action) . '" class="inline" data-confirm="' . e($confirm) . '">' . csrf_field();
    foreach ($fields as $k => $v) {
        $h .= '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
    }
    return $h . '<button type="submit" class="btn-sm danger">' . e($label) . '</button></form>';
}
