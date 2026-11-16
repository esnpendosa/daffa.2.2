<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();
$q = function ($sql) use ($pdo) { return (int) $pdo->query($sql)->fetchColumn(); };
$nPub = $q('SELECT COUNT(*) FROM articles WHERE status = 1');
$nDraft = $q('SELECT COUNT(*) FROM articles WHERE status = 0');
$nKom = $q('SELECT COUNT(*) FROM commodities');
$nMsg = $q('SELECT COUNT(*) FROM messages');
$nUnread = unread_count();
$msgs = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT 5')->fetchAll();
$arts = $pdo->query('SELECT * FROM articles ORDER BY published_at DESC, id DESC LIMIT 5')->fetchAll();
$warn = array();
if (!is_writable(PHOTO_DIR)) {
    $warn[] = 'Folder assets/photos/ tidak bisa ditulis server, jadi unggah foto kategori akan gagal (chmod 755/775).';
}
admin_head('Dashboard', 'dashboard');
foreach ($warn as $w) {
    echo '<div class="flash err">' . e($w) . '</div>';
}
?>
<div class="stats">
 <a class="stat" href="artikel.php"><b><?= $nPub ?></b><span>Artikel terbit<?= $nDraft ? ' · ' . $nDraft . ' draf' : '' ?></span></a>
 <a class="stat" href="kategori.php"><b><?= count(categories()) ?></b><span>Kategori</span></a>
 <a class="stat" href="kategori.php"><b><?= $nKom ?></b><span>Komoditas</span></a>
 <a class="stat<?= $nUnread ? ' hot' : '' ?>" href="pesan.php"><b><?= $nUnread ?></b><span>Pesan belum dibaca · <?= $nMsg ?> total</span></a>
</div>
<div class="grid2">
 <section class="card">
  <div class="card-head"><h2>Pesan terbaru</h2><a href="pesan.php">Semua →</a></div>
  <?php if (!$msgs) : ?><p class="muted">Belum ada pesan.</p><?php else : ?>
  <ul class="list"><?php foreach ($msgs as $m) : ?><li><a href="pesan.php?id=<?= (int) $m['id'] ?>"<?= $m['is_read'] ? '' : ' class="unread"' ?>><strong><?= e($m['name']) ?></strong> <span class="muted">· <?= e($m['topic']) ?></span><small><?= e(tgl_waktu_id($m['created_at'])) ?></small></a></li><?php endforeach; ?></ul>
  <?php endif; ?>
 </section>
 <section class="card">
  <div class="card-head"><h2>Artikel terbaru</h2><a href="artikel.php">Kelola →</a></div>
  <ul class="list"><?php foreach ($arts as $a) : ?><li><a href="artikel-edit.php?id=<?= (int) $a['id'] ?>"><strong><?= e($a['title']) ?></strong><small><?= e(category_label($a['category'])) ?> · <?= e(tgl_id($a['published_at'])) ?><?= $a['status'] ? '' : ' · DRAF' ?></small></a></li><?php endforeach; ?></ul>
  <p><a class="btn" href="artikel-edit.php">+ Tulis artikel</a></p>
 </section>
</div>
<?php admin_foot();
