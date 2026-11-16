<?php
require __DIR__ . '/_boot.php';
if (!admin_users_exist()) {
    redirect('setup.php');
}
if (!empty($_SESSION['uid'])) {
    redirect('index.php');
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $ip = client_ip();
    $pdo = db();
    $pdo->prepare('DELETE FROM login_attempts WHERE ts < ?')->execute(array(time() - 900));
    $c = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ?');
    $c->execute(array($ip));
    if ((int) $c->fetchColumn() >= 5) {
        $err = 'Terlalu banyak percobaan gagal. Coba lagi dalam 15 menit.';
    } else {
        $st = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $st->execute(array(post('username')));
        $u = $st->fetch();
        if ($u && password_verify(isset($_POST['password']) ? (string) $_POST['password'] : '', $u['password_hash'])) {
            $pdo->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute(array($ip));
            admin_login($u['id'], $u['username']);
            redirect('index.php');
        }
        $pdo->prepare('INSERT INTO login_attempts (ip, ts) VALUES (?, ?)')->execute(array($ip, time()));
        $err = 'Nama pengguna atau kata sandi salah.';
    }
} elseif (isset($_GET['exp'])) {
    $err = 'Sesi berakhir karena tidak aktif. Silakan masuk lagi.';
}
$title = 'Masuk';
require __DIR__ . '/_auth_page.php';
?>
<form method="post" class="auth-form" autocomplete="on">
 <?= csrf_field() ?>
 <label>Nama pengguna<input type="text" name="username" autocomplete="username" required autofocus></label>
 <label>Kata sandi<input type="password" name="password" autocomplete="current-password" required></label>
 <button type="submit" class="btn">Masuk</button>
</form>
<?php auth_foot();
