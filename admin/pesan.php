<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) post('id');
    $act = post('act');
    if ($act === 'hapus') {
        $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute(array($id));
        flash('Pesan dihapus.');
    } elseif ($act === 'belum') {
        $pdo->prepare('UPDATE messages SET is_read = 0 WHERE id = ?')->execute(array($id));
        flash('Ditandai belum dibaca.');
    } elseif ($act === 'semua') {
        $pdo->exec('UPDATE messages SET is_read = 1');
        flash('Semua pesan ditandai sudah dibaca.');
    }
    redirect('pesan.php');
}

$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
if ($id) {
    $st = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
    $st->execute(array($id));
    $m = $st->fetch();
    if (!$m) {
        flash('Pesan tidak ditemukan.', 'err');
        redirect('pesan.php');
    }
    $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute(array($id));
    admin_head('Pesan dari ' . $m['name'], 'pesan');
    $subject = rawurlencode('Re: [' . $m['topic'] . '] pesan Anda ke AgroNusa');
    ?>
<p><a class="btn ghost" href="pesan.php">← Semua pesan</a></p>
<article class="card msg">
 <dl class="meta">
  <div><dt>Nama</dt><dd><?= e($m['name']) ?></dd></div>
  <div><dt>Email</dt><dd><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></dd></div>
  <div><dt>Topik</dt><dd><?= e($m['topic']) ?></dd></div>
  <div><dt>Diterima</dt><dd><?= e(tgl_waktu_id($m['created_at'])) ?></dd></div>
 </dl>
 <div class="msg-body"><?= nl2br(e($m['body'])) ?></div>
 <div class="form-actions">
  <a class="btn" href="mailto:<?= e($m['email']) ?>?subject=<?= $subject ?>">Balas lewat email</a>
  <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="act" value="belum"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button type="submit" class="btn ghost">Tandai belum dibaca</button></form>
  <?= delete_form('pesan.php', array('act' => 'hapus', 'id' => $m['id']), 'Hapus', 'Hapus pesan ini?') ?>
 </div>
</article>
<?php
    admin_foot();
    exit;
}

$per = 25;
$page = max(1, (int) (isset($_GET['p']) ? $_GET['p'] : 1));
$total = (int) $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$pages = max(1, (int) ceil($total / $per));
$page = min($page, $pages);
$rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT ' . $per . ' OFFSET ' . (($page - 1) * $per))->fetchAll();
admin_head('Pesan masuk', 'pesan');
?>
<?php if ($total && unread_count()) : ?><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="act" value="semua"><button type="submit" class="btn ghost">Tandai semua sudah dibaca</button></form><?php endif; ?>
<div class="card table-card" style="margin-top:14px">
<?php if (!$rows) : ?><p class="muted" style="padding:20px">Belum ada pesan masuk dari formulir kontak.</p><?php else : ?>
<div class="table-scroll"><table class="tbl">
 <thead><tr><th>Pengirim</th><th>Topik</th><th>Cuplikan</th><th>Diterima</th><th class="r">Aksi</th></tr></thead>
 <tbody>
 <?php foreach ($rows as $m) : ?>
 <tr class="<?= $m['is_read'] ? '' : 'unread-row' ?>">
  <td><a href="pesan.php?id=<?= (int) $m['id'] ?>"><strong><?= e($m['name']) ?></strong></a><br><small class="muted"><?= e($m['email']) ?></small></td>
  <td><?= e($m['topic']) ?></td>
  <td><?= e(mb_strimwidth(preg_replace('/\s+/', ' ', $m['body']), 0, 70, '…')) ?></td>
  <td><?= e(tgl_waktu_id($m['created_at'])) ?></td>
  <td class="r actions"><a class="btn-sm" href="pesan.php?id=<?= (int) $m['id'] ?>">Buka</a><?= delete_form('pesan.php', array('act' => 'hapus', 'id' => $m['id']), 'Hapus', 'Hapus pesan ini?') ?></td>
 </tr>
 <?php endforeach; ?>
 </tbody>
</table></div>
<?php endif; ?>
</div>
<?php if ($pages > 1) : ?><nav class="pager" aria-label="Halaman"><?php for ($i = 1; $i <= $pages; $i++) : ?><a href="pesan.php?p=<?= $i ?>"<?= $i === $page ? ' aria-current="page"' : '' ?>><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
<?php admin_foot();
