<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();

function clean_url($v)
{
    $v = trim($v);
    if ($v === '' || $v === '#') {
        return '#';
    }
    return preg_match('#^https?://[^\s"\'<>]+$#i', $v) ? $v : false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = post('act');

    if ($act === 'setelan') {
        $email = post('site_email');
        $wa = preg_replace('/\D+/', '', post('wa_number'));
        $errs = array();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errs[] = 'Email tidak valid.';
        }
        if ($wa === '' || strlen($wa) < 8) {
            $errs[] = 'Nomor WhatsApp tidak valid (pakai format 628xxxx).';
        }
        $urls = array();
        foreach (array('instagram', 'youtube', 'facebook', 'tiktok') as $k) {
            $u = clean_url(post($k));
            if ($u === false) {
                $errs[] = 'Tautan ' . ucfirst($k) . ' harus diawali http:// atau https:// (atau isi #).';
            } else {
                $urls[$k] = $u;
            }
        }
        if ($errs) {
            flash(implode(' ', $errs), 'err');
            redirect('pengaturan.php');
        }
        $pdo->beginTransaction();
        save_setting('site_email', $email);
        save_setting('wa_number', $wa);
        save_setting('wa_display', post('wa_display'));
        foreach ($urls as $k => $u) {
            save_setting($k, $u);
        }
        save_setting('hero_eyebrow', post('hero_eyebrow'));
        save_setting('hero_title', post('hero_title'));
        save_setting('hero_lead', post('hero_lead'));
        save_setting('footer_desc', post('footer_desc'));
        save_setting('footer_note', post('footer_note'));
        save_setting('notify_mail', isset($_POST['notify_mail']) ? '1' : '0');
        $pdo->commit();
        flash('Pengaturan disimpan.');
        redirect('pengaturan.php');
    }

    if ($act === 'sandi') {
        $st = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $st->execute(array((int) $_SESSION['uid']));
        $u = $st->fetch();
        $cur = isset($_POST['cur']) ? (string) $_POST['cur'] : '';
        $n1 = isset($_POST['new1']) ? (string) $_POST['new1'] : '';
        $n2 = isset($_POST['new2']) ? (string) $_POST['new2'] : '';
        if (!$u || !password_verify($cur, $u['password_hash'])) {
            flash('Kata sandi saat ini salah.', 'err');
        } elseif (strlen($n1) < 8) {
            flash('Kata sandi baru minimal 8 karakter.', 'err');
        } elseif ($n1 !== $n2) {
            flash('Konfirmasi kata sandi baru tidak sama.', 'err');
        } else {
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute(array(password_hash($n1, PASSWORD_DEFAULT), $u['id']));
            session_regenerate_id(true);
            flash('Kata sandi diganti.');
        }
        redirect('pengaturan.php#sandi');
    }
}

$s = settings();
admin_head('Pengaturan', 'pengaturan');
?>
<form method="post" class="card form">
 <?= csrf_field() ?><input type="hidden" name="act" value="setelan">
 <h2>Kontak</h2>
 <div class="row2">
  <label>Email<input type="email" name="site_email" value="<?= e($s['site_email']) ?>" required></label>
  <label>Nomor WhatsApp (format 628xxxx)<input type="text" name="wa_number" value="<?= e($s['wa_number']) ?>" inputmode="numeric" required></label>
 </div>
 <label>Tampilan nomor WhatsApp<input type="text" name="wa_display" value="<?= e($s['wa_display']) ?>"></label>
 <label class="check"><input type="checkbox" name="notify_mail" value="1"<?= $s['notify_mail'] === '1' ? ' checked' : '' ?>> Kirim juga salinan pesan formulir ke email di atas (butuh fungsi mail() aktif di hosting)</label>
 <h2>Media sosial</h2>
 <div class="row2">
  <label>Instagram<input type="text" name="instagram" value="<?= e($s['instagram']) ?>" placeholder="https://instagram.com/..."></label>
  <label>YouTube<input type="text" name="youtube" value="<?= e($s['youtube']) ?>" placeholder="https://youtube.com/..."></label>
 </div>
 <div class="row2">
  <label>Facebook<input type="text" name="facebook" value="<?= e($s['facebook']) ?>" placeholder="https://facebook.com/..."></label>
  <label>TikTok<input type="text" name="tiktok" value="<?= e($s['tiktok']) ?>" placeholder="https://tiktok.com/@..."></label>
 </div>
 <h2>Teks beranda</h2>
 <label>Label kecil di atas judul<input type="text" name="hero_eyebrow" value="<?= e($s['hero_eyebrow']) ?>"></label>
 <label>Judul utama<input type="text" name="hero_title" value="<?= e($s['hero_title']) ?>"></label>
 <label>Kalimat pembuka<textarea name="hero_lead" rows="3"><?= e($s['hero_lead']) ?></textarea></label>
 <h2>Footer</h2>
 <label>Deskripsi singkat<textarea name="footer_desc" rows="2"><?= e($s['footer_desc']) ?></textarea></label>
 <label>Catatan hak cipta/penafian<input type="text" name="footer_note" value="<?= e($s['footer_note']) ?>"></label>
 <div class="form-actions"><button type="submit" class="btn">Simpan pengaturan</button></div>
</form>

<form method="post" class="card form" id="sandi" autocomplete="off">
 <?= csrf_field() ?><input type="hidden" name="act" value="sandi">
 <h2>Ganti kata sandi</h2>
 <label>Kata sandi saat ini<input type="password" name="cur" autocomplete="current-password" required></label>
 <div class="row2">
  <label>Kata sandi baru (min. 8 karakter)<input type="password" name="new1" autocomplete="new-password" minlength="8" required></label>
  <label>Ulangi kata sandi baru<input type="password" name="new2" autocomplete="new-password" minlength="8" required></label>
 </div>
 <div class="form-actions"><button type="submit" class="btn">Ganti kata sandi</button></div>
</form>
<?php admin_foot();
