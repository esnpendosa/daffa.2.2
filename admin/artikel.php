<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) post('id');
    $act = post('act');
    if ($act === 'hapus') {
        $pdo->prepare('DELETE FROM articles WHERE id = ?')->execute(array($id));
        flash('Artikel dihapus.');
    } elseif ($act === 'toggle') {
        $pdo->prepare('UPDATE articles SET status = 1 - status WHERE id = ?')->execute(array($id));
        flash('Status artikel diperbarui.');
    }
    redirect('artikel.php');
}

$rows = $pdo->query('SELECT * FROM articles ORDER BY published_at DESC, id DESC')->fetchAll();
admin_head('Artikel', 'artikel');
?>
<p><a class="btn" href="artikel-edit.php">+ Tulis artikel</a></p>
<div class="card table-card">
<?php if (!$rows) : ?><p class="muted" style="padding:20px">Belum ada artikel.</p><?php else : ?>
<div class="table-scroll"><table class="tbl">
 <thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th class="r">Aksi</th></tr></thead>
 <tbody>
 <?php foreach ($rows as $a) : ?>
 <tr>
  <td><a href="artikel-edit.php?id=<?= (int) $a['id'] ?>"><strong><?= e($a['title']) ?></strong></a><br><small class="muted"><?= e($a['slug']) ?></small></td>
  <td><?= e(category_label($a['category'])) ?></td>
  <td><?= e(tgl_id($a['published_at'])) ?></td>
  <td><span class="pill <?= $a['status'] ? 'ok' : 'off' ?>"><?= $a['status'] ? 'Terbit' : 'Draf' ?></span></td>
  <td class="r actions">
   <a class="btn-sm" href="artikel-edit.php?id=<?= (int) $a['id'] ?>">Edit</a>
   <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= (int) $a['id'] ?>"><button type="submit" class="btn-sm"><?= $a['status'] ? 'Jadikan draf' : 'Terbitkan' ?></button></form>
   <?= delete_form('artikel.php', array('act' => 'hapus', 'id' => $a['id']), 'Hapus', 'Hapus artikel “' . $a['title'] . '”?') ?>
  </td>
 </tr>
 <?php endforeach; ?>
 </tbody>
</table></div>
<?php endif; ?>
</div>
<?php admin_foot();
