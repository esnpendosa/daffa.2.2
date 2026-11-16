<?php
require __DIR__ . '/_boot.php';
if (admin_users_exist()) {
    redirect('login.php');
}
$err = '';
$uname = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $uname = post('username');
    $p1 = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $p2 = isset($_POST['password2']) ? (string) $_POST['password2'] : '';
    if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $uname)) {
        $err = 'Nama pengguna 3–30 karakter: huruf, angka, titik, garis bawah, atau strip.';
    } elseif (strlen($p1) < 8) {
        $err = 'Kata sandi minimal 8 karakter.';
    } elseif ($p1 !== $p2) {
        $err = 'Konfirmasi kata sandi tidak sama.';
    } else {
        $st = db()->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, ?)');
        $st->execute(array($uname, password_hash($p1, PASSWORD_DEFAULT), date('Y-m-d H:i:s')));
        admin_login(db()->lastInsertId(), $uname);
        flash('Akun admin dibuat. Selamat datang!');
        redirect('index.php');
    }
}
$title = 'Buat akun admin';
$intro = 'Ini pemasangan pertama. Buat akun admin untuk mengelola situs.';
require __DIR__ . '/_auth_page.php';
?>
<form method="post" class="auth-form" autocomplete="off">
 <?= csrf_field() ?>
 <label>Nama pengguna<input type="text" name="username" value="<?= e($uname) ?>" required autofocus></label>
 <label>Kata sandi (min. 8 karakter)<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
 <label>Ulangi kata sandi<input type="password" name="password2" autocomplete="new-password" minlength="8" required></label>
 <button type="submit" class="btn">Buat akun</button>
</form>
<?php auth_foot();
