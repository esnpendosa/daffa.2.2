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
$back = 'kategori-edit.php?id=' . $cat . '#komoditas';
$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$k = array('id' => 0, 'name' => '', 'icon' => $c['icon'], 'description' => '', 'facts' => '[]', 'featured' => 0, 'sort' => count(commodities_of($cat)) + 1);
if ($id) {
    $st = $pdo->prepare('SELECT * FROM commodities WHERE id = ? AND category_id = ?');
    $st->execute(array($id, $cat));
    $row = $st->fetch();
    if (!$row) {
        flash('Komoditas tidak ditemukan.', 'err');
        redirect($back);
    }
    $k = $row;
}
$facts = json_decode($k['facts'], true);
if (!is_array($facts)) {
    $facts = array();
}
while (count($facts) < 3) {
    $facts[] = array('', '');
}
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $k['name'] = post('name');
    $icons = icon_list();
    $k['icon'] = in_array(post('icon'), $icons, true) ? post('icon') : $c['icon'];
    $k['description'] = post('description');
    $k['featured'] = isset($_POST['featured']) ? 1 : 0;
    $k['sort'] = (int) post('sort');
    $facts = array();
    for ($i = 0; $i < 3; $i++) {
        $facts[] = array(post('fl' . $i), post('fv' . $i));
    }
    if ($k['name'] === '') {
        $err = 'Nama komoditas wajib diisi.';
    } else {
        $clean = array();
        foreach ($facts as $f) {
            if ($f[0] !== '' || $f[1] !== '') {
                $clean[] = $f;
            }
        }
        $json = json_encode($clean, JSON_UNESCAPED_UNICODE);
        if ($k['id']) {
            $pdo->prepare('UPDATE commodities SET name=?, icon=?, description=?, facts=?, featured=?, sort=? WHERE id=?')
                ->execute(array($k['name'], $k['icon'], $k['description'], $json, $k['featured'], $k['sort'], $k['id']));
        } else {
            $pdo->prepare('INSERT INTO commodities (category_id, name, icon, description, facts, featured, sort) VALUES (?,?,?,?,?,?,?)')
                ->execute(array($cat, $k['name'], $k['icon'], $k['description'], $json, $k['featured'], $k['sort']));
        }
        flash('Komoditas disimpan.');
        redirect($back);
    }
}

admin_head(($k['id'] ? 'Edit komoditas' : 'Komoditas baru') . ' · ' . $c['name'], 'kategori');
if ($err) {
    echo '<div class="flash err" role="alert">' . e($err) . '</div>';
}
?>
<form method="post" class="card form">
 <?= csrf_field() ?>
 <div class="row2">
  <label>Nama<input type="text" name="name" value="<?= e($k['name']) ?>" maxlength="60" required></label>
  <label>Ikon<select name="icon"><?php foreach (icon_list() as $i) : ?><option value="<?= e($i) ?>"<?= $k['icon'] === $i ? ' selected' : '' ?>><?= e($i) ?></option><?php endforeach; ?></select></label>
 </div>
 <label>Deskripsi<textarea name="description" rows="3"><?= e($k['description']) ?></textarea></label>
 <h3>Fakta kunci</h3>
 <p class="muted">Tiga baris fakta yang tampil di kartu. Kosongkan baris yang tidak dipakai.</p>
 <?php for ($i = 0; $i < 3; $i++) : ?>
 <div class="row2"><label>Label <?= $i + 1 ?><input type="text" name="fl<?= $i ?>" value="<?= e($facts[$i][0]) ?>" maxlength="40"></label><label>Nilai <?= $i + 1 ?><input type="text" name="fv<?= $i ?>" value="<?= e($facts[$i][1]) ?>" maxlength="80"></label></div>
 <?php endfor; ?>
 <div class="row2">
  <label>Urutan<input type="number" name="sort" value="<?= (int) $k['sort'] ?>" min="0" max="99"></label>
  <label class="check" style="align-self:end"><input type="checkbox" name="featured" value="1"<?= $k['featured'] ? ' checked' : '' ?>> Kartu disorot (latar gelap)</label>
 </div>
 <div class="form-actions"><button type="submit" class="btn">Simpan</button><a class="btn ghost" href="<?= e($back) ?>">Batal</a></div>
</form>
<?php admin_foot();
