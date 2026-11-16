<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();
$cat = (int) (isset($_GET['cat']) ? $_GET['cat'] : 0);
$c = category_by_id($cat);
if (!$c) {
    flash('Kategori tidak ditemukan.', 'err');
    redirect('kategori.php');
}
$back = 'kategori-edit.php?id=' . $cat . '#tips';
$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$t = array('id' => 0, 'title' => '', 'text' => '', 'sort' => count(tips_of($cat)) + 1);
if ($id) {
    $st = $pdo->prepare('SELECT * FROM tips WHERE id = ? AND category_id = ?');
    $st->execute(array($id, $cat));
    $row = $st->fetch();
    if (!$row) {
        flash('Tips tidak ditemukan.', 'err');
        redirect($back);
    }
    $t = $row;
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $t['title'] = post('title');
    $t['text'] = post('text');
    $t['sort'] = (int) post('sort');
    if ($t['title'] === '') {
        $err = 'Judul tips wajib diisi.';
    } else {
        if ($t['id']) {
            $pdo->prepare('UPDATE tips SET title=?, text=?, sort=? WHERE id=?')->execute(array($t['title'], $t['text'], $t['sort'], $t['id']));
        } else {
            $pdo->prepare('INSERT INTO tips (category_id, title, text, sort) VALUES (?,?,?,?)')->execute(array($cat, $t['title'], $t['text'], $t['sort']));
        }
        flash('Tips disimpan.');
        redirect($back);
    }
}
admin_head(($t['id'] ? 'Edit tips' : 'Tips baru') . ' · ' . $c['name'], 'kategori');
if ($err) {
    echo '<div class="flash err" role="alert">' . e($err) . '</div>';
}
?>
<form method="post" class="card form">
 <?= csrf_field() ?>
 <label>Judul tips<input type="text" name="title" value="<?= e($t['title']) ?>" maxlength="100" required></label>
 <label>Penjelasan<textarea name="text" rows="3"><?= e($t['text']) ?></textarea></label>
 <label>Urutan<input type="number" name="sort" value="<?= (int) $t['sort'] ?>" min="0" max="99"></label>
 <div class="form-actions"><button type="submit" class="btn">Simpan</button><a class="btn ghost" href="<?= e($back) ?>">Batal</a></div>
</form>
<?php admin_foot();
