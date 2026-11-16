<?php
require __DIR__ . '/includes/layout.php';
start_session();

$topics = array('Pertanyaan umum', 'Pertanian', 'Perkebunan', 'Peternakan', 'Perikanan', 'Teknologi Agro', 'Kerja sama');
$status = '';
$old = array('nama' => '', 'email' => '', 'topik' => $topics[0], 'pesan' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $old = array('nama' => post('nama'), 'email' => post('email'), 'topik' => post('topik'), 'pesan' => post('pesan'));
    $now = time();
    if (post('website') !== '') {
        // kolom jebakan untuk bot: pura-pura berhasil
        redirect('kontak.php?terkirim=1#form-kontak');
    } elseif (!empty($_SESSION['last_msg']) && $now - $_SESSION['last_msg'] < 20) {
        $status = 'Mohon tunggu sebentar sebelum mengirim pesan lagi.';
    } elseif ($old['nama'] === '' || $old['pesan'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $status = 'Mohon isi nama, email yang valid, dan pesan.';
    } elseif (!in_array($old['topik'], $topics, true)) {
        $status = 'Topik tidak dikenali.';
    } else {
        $st = db()->prepare('INSERT INTO messages (name, email, topic, body, ip, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        $st->execute(array(
            mb_substr($old['nama'], 0, 120), mb_substr($old['email'], 0, 160), $old['topik'],
            mb_substr($old['pesan'], 0, 5000), client_ip(), date('Y-m-d H:i:s'),
        ));
        $_SESSION['last_msg'] = $now;
        if (setting('notify_mail') === '1' && setting('site_email') !== '') {
            $subject = '[' . $old['topik'] . '] Pesan dari ' . str_replace(array("\r", "\n"), ' ', $old['nama']);
            $body = "Nama: " . $old['nama'] . "\nEmail: " . $old['email'] . "\n\n" . $old['pesan'];
            $headers = 'Reply-To: ' . str_replace(array("\r", "\n"), '', $old['email']) . "\r\nContent-Type: text/plain; charset=UTF-8";
            @mail(setting('site_email'), '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
        }
        redirect('kontak.php?terkirim=1#form-kontak');
    }
} elseif (isset($_GET['terkirim'])) {
    $status = 'Terima kasih, pesan Anda sudah kami terima. Kami akan membalas lewat email.';
}

$wa = preg_replace('/\D+/', '', setting('wa_number'));
page_head('Kontak | AgroNusa', 'Hubungi AgroNusa lewat formulir, email, WhatsApp, atau media sosial.', 'kontak');
echo page_hero('umum', array(array('Beranda', 'index.php'), array('Kontak', null)), 'Ada yang ingin ditanyakan?', 'Kirim pesan lewat formulir, email, atau WhatsApp.');
?>
<section class="section"><div class="container contact-grid">
 <form class="form rv" id="form-kontak" method="post" action="kontak.php#form-kontak"><h2>Kirim pesan</h2>
  <?= csrf_field() ?>
  <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
  <div class="field"><label for="nama">Nama</label><input id="nama" name="nama" type="text" autocomplete="name" maxlength="120" value="<?= e($old['nama']) ?>" required></div>
  <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" maxlength="160" value="<?= e($old['email']) ?>" required></div>
  <div class="field"><label for="topik">Topik</label><select id="topik" name="topik"><?php foreach ($topics as $t) : ?><option<?= $old['topik'] === $t ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="pesan">Pesan</label><textarea id="pesan" name="pesan" maxlength="5000" required><?= e($old['pesan']) ?></textarea></div>
  <button class="btn btn-lg" type="submit">Kirim pesan</button><p class="form-status" id="form-status" role="status"><?= e($status) ?></p></form>
 <div class="contact-list">
  <a class="contact-item rv" href="mailto:<?= e(setting('site_email')) ?>"><span class="icon-box"><img src="assets/icons/mail.svg" alt=""></span><div><strong>Email</strong><span><?= e(setting('site_email')) ?></span></div></a>
  <a class="contact-item rv" href="https://wa.me/<?= e($wa) ?>"><span class="icon-box"><img src="assets/icons/chat.svg" alt=""></span><div><strong>WhatsApp</strong><span><?= e(setting('wa_display')) ?></span></div></a>
  <div class="social rv"><h3>Media sosial</h3><div><a href="<?= e(setting('instagram')) ?>">Instagram</a><a href="<?= e(setting('youtube')) ?>">YouTube</a><a href="<?= e(setting('facebook')) ?>">Facebook</a><a href="<?= e(setting('tiktok')) ?>">TikTok</a></div></div>
 </div></div></section>
<?php page_foot();
