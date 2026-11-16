<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();
$id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$st = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$st->execute(array($id));
$c = $st->fetch();
if (!$c) {
    flash('Kategori tidak ditemukan.', 'err');
    redirect('kategori.php');
}
$self = 'kategori-edit.php?id=' . $id;

/** Hapus semua varian foto kategori. */
function remove_photos($slug)
{
    foreach (array('jpg', 'jpeg', 'png', 'webp') as $x) {
        $f = PHOTO_DIR . '/' . $slug . '.' . $x;
        if (is_file($f)) {
            @unlink($f);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = post('act');

    if ($act === 'info') {
        $color = post('color');
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = $c['color'];
        }
        $icons = icon_list();
        $icon = in_array(post('icon'), $icons, true) ? post('icon') : $c['icon'];
        $cta2 = post('cta2_url');
        if (!preg_match('/^[A-Za-z0-9._\-\/#?=]+$/', $cta2)) {
            $cta2 = 'kategori.php';
        }
        // tabel perbandingan: baris dipisah enter, kolom dipisah |
        $rows = array();
        foreach (preg_split('/\r\n|\r|\n/', (string) (isset($_POST['cmp_table']) ? $_POST['cmp_table'] : '')) as $ln) {
            if (trim($ln) === '') {
                continue;
            }
            $rows[] = array_map('trim', explode('|', $ln));
        }
        $compare = null;
        if (count($rows) >= 2) {
            $compare = json_encode(array(
                'kicker' => post('cmp_kicker'), 'title' => post('cmp_title'), 'note' => post('cmp_note'), 'rows' => $rows,
            ), JSON_UNESCAPED_UNICODE);
        }
        $name = post('name');
        if ($name === '') {
            flash('Nama kategori wajib diisi.', 'err');
            redirect($self);
        }
        $pdo->prepare('UPDATE categories SET name=?, sort=?, arch_sort=?, color=?, icon=?, short_desc=?, seo_desc=?, hero_lead=?,
            kom_kicker=?, kom_title=?, kom_note=?, tips_kicker=?, tips_title=?, tips_intro=?, compare=?, show_related=?,
            cta_title=?, cta_text=?, cta2_label=?, cta2_url=? WHERE id=?')->execute(array(
            $name, (int) post('sort'), (int) post('arch_sort'), $color, $icon, post('short_desc'), post('seo_desc'), post('hero_lead'),
            post('kom_kicker'), post('kom_title'), post('kom_note'), post('tips_kicker'), post('tips_title'), post('tips_intro'),
            $compare, isset($_POST['show_related']) ? 1 : 0,
            post('cta_title'), post('cta_text'), post('cta2_label'), $cta2, $id,
        ));
        flash('Kategori disimpan.');
        redirect($self);
    }

    if ($act === 'foto') {
        $f = isset($_FILES['foto']) ? $_FILES['foto'] : null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
            flash('Pilih file foto dulu.', 'err');
        } elseif ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > MAX_UPLOAD_BYTES) {
            flash('Unggahan gagal atau lebih dari 3 MB.', 'err');
        } else {
            $info = @getimagesize($f['tmp_name']);
            $types = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp');
            if (!$info || !isset($types[$info[2]])) {
                flash('Format harus JPG, PNG, atau WebP.', 'err');
            } elseif (!is_dir(PHOTO_DIR) && !@mkdir(PHOTO_DIR, 0755, true)) {
                flash('Folder assets/photos/ tidak bisa dibuat.', 'err');
            } else {
                remove_photos($c['slug']);
                $dest = PHOTO_DIR . '/' . $c['slug'] . '.' . $types[$info[2]];
                if (move_uploaded_file($f['tmp_name'], $dest)) {
                    @chmod($dest, 0644);
                    flash('Foto diganti. Foto ini menggantikan ilustrasi di seluruh situs.');
                } else {
                    flash('Gagal menyimpan foto. Periksa izin folder assets/photos/.', 'err');
                }
            }
        }
        redirect($self . '#foto');
    }

    if ($act === 'foto_hapus') {
        remove_photos($c['slug']);
        flash('Foto dihapus; ilustrasi bawaan dipakai kembali.');
        redirect($self . '#foto');
    }

    if ($act === 'kom_hapus') {
        $pdo->prepare('DELETE FROM commodities WHERE id = ? AND category_id = ?')->execute(array((int) post('kid'), $id));
        flash('Komoditas dihapus.');
        redirect($self . '#komoditas');
    }

    if ($act === 'tip_hapus') {
        $pdo->prepare('DELETE FROM tips WHERE id = ? AND category_id = ?')->execute(array((int) post('tid'), $id));
        flash('Tips dihapus.');
        redirect($self . '#tips');
    }
}

$kom = commodities_of($id);
$tips = tips_of($id);
$cmp = !empty($c['compare']) ? json_decode($c['compare'], true) : array();
$cmpText = '';
if (!empty($cmp['rows'])) {
    $lines = array();
    foreach ($cmp['rows'] as $r) {
        $lines[] = implode(' | ', $r);
    }
    $cmpText = implode("\n", $lines);
}
$hasPhoto = strpos(banner($c['slug'], 'css'), 'assets/photos/') !== false;

admin_head('Kategori: ' . $c['name'], 'kategori');
?>
<p><a class="btn ghost" href="kategori.php">← Semua kategori</a> <a class="btn ghost" href="../<?= e($c['slug']) ?>.php" target="_blank" rel="noopener">Lihat halaman ↗</a></p>

<form method="post" class="card form">
 <?= csrf_field() ?><input type="hidden" name="act" value="info">
 <h2>Teks dan tampilan</h2>
 <div class="row2">
  <label>Nama kategori<input type="text" name="name" value="<?= e($c['name']) ?>" maxlength="60" required></label>
  <label>Ikon<select name="icon"><?php foreach (icon_list() as $i) : ?><option value="<?= e($i) ?>"<?= $c['icon'] === $i ? ' selected' : '' ?>><?= e($i) ?></option><?php endforeach; ?></select></label>
 </div>
 <div class="row3">
  <label>Warna titik/aksen<input type="color" name="color" value="<?= e($c['color']) ?>"></label>
  <label>Urutan menu<input type="number" name="sort" value="<?= (int) $c['sort'] ?>" min="0" max="99"></label>
  <label>Urutan lengkungan beranda<input type="number" name="arch_sort" value="<?= (int) $c['arch_sort'] ?>" min="0" max="99"></label>
 </div>
 <label>Deskripsi singkat<textarea name="short_desc" rows="2"><?= e($c['short_desc']) ?></textarea><small class="muted">Tampil di beranda (akordeon) dan halaman Kategori.</small></label>
 <label>Kalimat pembuka halaman<textarea name="hero_lead" rows="2"><?= e($c['hero_lead']) ?></textarea></label>
 <label>Deskripsi SEO (meta description)<textarea name="seo_desc" rows="2"><?= e($c['seo_desc']) ?></textarea></label>

 <h3>Bagian komoditas</h3>
 <div class="row3">
  <label>Label kecil<input type="text" name="kom_kicker" value="<?= e($c['kom_kicker']) ?>"></label>
  <label>Judul<input type="text" name="kom_title" value="<?= e($c['kom_title']) ?>"></label>
  <label>Catatan<input type="text" name="kom_note" value="<?= e($c['kom_note']) ?>"></label>
 </div>

 <h3>Bagian tips</h3>
 <div class="row2">
  <label>Label kecil<input type="text" name="tips_kicker" value="<?= e($c['tips_kicker']) ?>"></label>
  <label>Judul<input type="text" name="tips_title" value="<?= e($c['tips_title']) ?>"></label>
 </div>
 <label>Pengantar<textarea name="tips_intro" rows="2"><?= e($c['tips_intro']) ?></textarea></label>

 <h3>Tabel perbandingan (opsional)</h3>
 <div class="row3">
  <label>Label kecil<input type="text" name="cmp_kicker" value="<?= e(isset($cmp['kicker']) ? $cmp['kicker'] : '') ?>"></label>
  <label>Judul<input type="text" name="cmp_title" value="<?= e(isset($cmp['title']) ? $cmp['title'] : '') ?>"></label>
  <label>Catatan di bawah tabel<input type="text" name="cmp_note" value="<?= e(isset($cmp['note']) ? $cmp['note'] : '') ?>"></label>
 </div>
 <label>Isi tabel<textarea name="cmp_table" rows="5" placeholder="Teknologi | Cocok untuk | Modal awal&#10;Hidroponik | Lahan sempit | Rendah"><?= e($cmpText) ?></textarea><small class="muted">Satu baris per baris tabel, kolom dipisah tanda |. Baris pertama = judul kolom. Kosongkan untuk menyembunyikan tabel.</small></label>

 <h3>Artikel terkait dan ajakan bawah halaman</h3>
 <label class="check"><input type="checkbox" name="show_related" value="1"<?= $c['show_related'] ? ' checked' : '' ?>> Tampilkan artikel terkait kategori ini</label>
 <div class="row2">
  <label>Judul ajakan<input type="text" name="cta_title" value="<?= e($c['cta_title']) ?>"></label>
  <label>Teks ajakan<input type="text" name="cta_text" value="<?= e($c['cta_text']) ?>"></label>
 </div>
 <div class="row2">
  <label>Tombol kedua: tulisan<input type="text" name="cta2_label" value="<?= e($c['cta2_label']) ?>"></label>
  <label>Tombol kedua: tujuan<input type="text" name="cta2_url" value="<?= e($c['cta2_url']) ?>"></label>
 </div>
 <div class="form-actions"><button type="submit" class="btn">Simpan perubahan</button></div>
</form>

<section class="card" id="foto">
 <h2>Foto kategori</h2>
 <p class="muted">Foto asli menggantikan ilustrasi untuk kategori ini di seluruh situs (banner, lengkungan, kartu artikel). JPG/PNG/WebP, maksimal 3 MB, disarankan landscape ±1600 px.</p>
 <div class="photo-row">
  <div class="photo-prev" style="background-image:url('../<?= e(preg_replace('/^\.\.\//', '', banner($c['slug'], 'css'))) ?>')"></div>
  <div>
   <form method="post" enctype="multipart/form-data" class="inline-form"><?= csrf_field() ?><input type="hidden" name="act" value="foto"><input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required><button type="submit" class="btn">Unggah</button></form>
   <?php if ($hasPhoto) : ?><?= delete_form($self, array('act' => 'foto_hapus'), 'Hapus foto (pakai ilustrasi bawaan)', 'Hapus foto dan kembali ke ilustrasi bawaan?') ?><?php else : ?><p class="muted"><small>Saat ini memakai ilustrasi bawaan.</small></p><?php endif; ?>
  </div>
 </div>
</section>

<section class="card" id="komoditas">
 <div class="card-head"><h2>Komoditas (<?= count($kom) ?>)</h2><a class="btn" href="komoditas-edit.php?cat=<?= $id ?>">+ Tambah</a></div>
 <?php if (!$kom) : ?><p class="muted">Belum ada komoditas.</p><?php else : ?>
 <div class="table-scroll"><table class="tbl"><thead><tr><th>Urut</th><th>Nama</th><th>Fakta kunci</th><th class="r">Aksi</th></tr></thead><tbody>
 <?php foreach ($kom as $k) :
     $facts = json_decode($k['facts'], true);
     $ft = array();
     if (is_array($facts)) {
         foreach ($facts as $f) {
             $ft[] = $f[0] . ': ' . $f[1];
         }
     }
     ?>
  <tr><td><?= (int) $k['sort'] ?></td><td><img class="ico" src="../assets/icons/<?= e($k['icon']) ?>.svg" alt="" width="22" height="22"> <strong><?= e($k['name']) ?></strong><?= $k['featured'] ? ' <span class="pill ok">Sorot</span>' : '' ?></td><td><small class="muted"><?= e(implode(' · ', $ft)) ?></small></td>
  <td class="r actions"><a class="btn-sm" href="komoditas-edit.php?cat=<?= $id ?>&amp;id=<?= (int) $k['id'] ?>">Edit</a><?= delete_form($self, array('act' => 'kom_hapus', 'kid' => $k['id']), 'Hapus', 'Hapus komoditas “' . $k['name'] . '”?') ?></td></tr>
 <?php endforeach; ?>
 </tbody></table></div>
 <?php endif; ?>
</section>

<section class="card" id="tips">
 <div class="card-head"><h2>Tips budidaya (<?= count($tips) ?>)</h2><a class="btn" href="tip-edit.php?cat=<?= $id ?>">+ Tambah</a></div>
 <?php if (!$tips) : ?><p class="muted">Belum ada tips.</p><?php else : ?>
 <div class="table-scroll"><table class="tbl"><thead><tr><th>Urut</th><th>Judul</th><th>Isi</th><th class="r">Aksi</th></tr></thead><tbody>
 <?php foreach ($tips as $t) : ?>
  <tr><td><?= (int) $t['sort'] ?></td><td><strong><?= e($t['title']) ?></strong></td><td><small class="muted"><?= e(mb_strimwidth($t['text'], 0, 90, '…')) ?></small></td>
  <td class="r actions"><a class="btn-sm" href="tip-edit.php?cat=<?= $id ?>&amp;id=<?= (int) $t['id'] ?>">Edit</a><?= delete_form($self, array('act' => 'tip_hapus', 'tid' => $t['id']), 'Hapus', 'Hapus tips “' . $t['title'] . '”?') ?></td></tr>
 <?php endforeach; ?>
 </tbody></table></div>
 <?php endif; ?>
</section>
<?php admin_foot();
