<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();
$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$a = array('id' => 0, 'slug' => '', 'category' => 'umum', 'title' => '', 'excerpt' => '', 'tips' => '', 'published_at' => date('Y-m-d'), 'status' => 1);
if ($id) {
    $st = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
    $st->execute(array($id));
    $row = $st->fetch();
    if (!$row) {
        flash('Artikel tidak ditemukan.', 'err');
        redirect('artikel.php');
    }
    $a = $row;
}
$cats = article_category_options();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $a['title'] = post('title');
    $a['category'] = post('category');
    $a['excerpt'] = post('excerpt');
    $a['tips'] = str_replace("\r", '', post('tips'));
    $a['published_at'] = post('published_at');
    $a['status'] = isset($_POST['status']) ? 1 : 0;
    $slugIn = slugify(post('slug'));
    $a['slug'] = $slugIn !== '' ? $slugIn : slugify($a['title']);

    $d = DateTime::createFromFormat('Y-m-d', $a['published_at']);
    if ($a['title'] === '') {
        $err = 'Judul wajib diisi.';
    } elseif (!isset($cats[$a['category']])) {
        $err = 'Kategori tidak valid.';
    } elseif (!$d || $d->format('Y-m-d') !== $a['published_at']) {
        $err = 'Tanggal tidak valid.';
    } elseif ($a['slug'] === '') {
        $err = 'Alamat (slug) tidak bisa dibuat dari judul ini; isi manual dengan huruf/angka.';
    } else {
        // slug harus unik
        $base = $a['slug'];
        $n = 2;
        while (true) {
            $c = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = ? AND id <> ?');
            $c->execute(array($a['slug'], (int) $a['id']));
            if ((int) $c->fetchColumn() === 0) {
                break;
            }
            $a['slug'] = $base . '-' . $n++;
        }
        if ($a['id']) {
            $pdo->prepare('UPDATE articles SET slug=?, category=?, title=?, excerpt=?, tips=?, published_at=?, status=? WHERE id=?')
                ->execute(array($a['slug'], $a['category'], $a['title'], $a['excerpt'], $a['tips'], $a['published_at'], $a['status'], $a['id']));
        } else {
            $pdo->prepare('INSERT INTO articles (slug, category, title, excerpt, tips, published_at, status) VALUES (?,?,?,?,?,?,?)')
                ->execute(array($a['slug'], $a['category'], $a['title'], $a['excerpt'], $a['tips'], $a['published_at'], $a['status']));
        }
        flash('Artikel disimpan.');
        redirect('artikel.php');
    }
}

admin_head($a['id'] ? 'Edit artikel' : 'Artikel baru', 'artikel');
if ($err) {
    echo '<div class="flash err" role="alert">' . e($err) . '</div>';
}
?>
<form method="post" class="card form">
 <?= csrf_field() ?>
 <label>Judul<input type="text" name="title" value="<?= e($a['title']) ?>" maxlength="200" required></label>
 <div class="row2">
  <label>Kategori<select name="category"><?php foreach ($cats as $slug => $name) : ?><option value="<?= e($slug) ?>"<?= $a['category'] === $slug ? ' selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?></select></label>
  <label>Tanggal terbit<input type="date" name="published_at" value="<?= e($a['published_at']) ?>" required></label>
 </div>
 <label>Ringkasan<textarea name="excerpt" rows="3" maxlength="400"><?= e($a['excerpt']) ?></textarea><small class="muted">Satu-dua kalimat yang tampil di kartu artikel.</small></label>
 <label>Daftar tips<textarea name="tips" rows="7"><?= e($a['tips']) ?></textarea><small class="muted">Satu tips per baris. Tampil sebagai daftar saat “Lihat tips” dibuka di halaman Artikel.</small></label>
 <label>Alamat/slug<input type="text" name="slug" value="<?= e($a['slug']) ?>" maxlength="80" pattern="[a-z0-9\-]*"><small class="muted">Opsional; dibuat otomatis dari judul. Dipakai sebagai tautan (artikel.php#slug).</small></label>
 <label class="check"><input type="checkbox" name="status" value="1"<?= $a['status'] ? ' checked' : '' ?>> Terbitkan (tampil di situs)</label>
 <div class="form-actions"><button type="submit" class="btn">Simpan</button><a class="btn ghost" href="artikel.php">Batal</a></div>
</form>
<?php admin_foot();
