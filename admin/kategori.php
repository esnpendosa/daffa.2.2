<?php
require __DIR__ . '/_boot.php';
require_admin();
$pdo = db();
admin_head('Kategori', 'kategori');
?>
<p class="muted">Lima kategori utama situs. Klik untuk mengubah teks, foto, komoditas, dan tips tiap kategori.</p>
<div class="cat-grid">
<?php foreach (categories() as $c) :
    $nk = $pdo->prepare('SELECT COUNT(*) FROM commodities WHERE category_id = ?');
    $nk->execute(array($c['id']));
    $nt = $pdo->prepare('SELECT COUNT(*) FROM tips WHERE category_id = ?');
    $nt->execute(array($c['id']));
    ?>
 <a class="card cat-card" href="kategori-edit.php?id=<?= (int) $c['id'] ?>" style="--c:<?= e($c['color']) ?>">
  <div class="cat-thumb" style="background-image:url('../<?= e(preg_replace('/^\.\.\//', '', banner($c['slug'], 'css'))) ?>')"></div>
  <div class="cat-info"><h2><img src="../assets/icons/<?= e($c['icon']) ?>.svg" alt="" width="24" height="24"><?= e($c['name']) ?></h2>
  <p class="muted"><?= (int) $nk->fetchColumn() ?> komoditas · <?= (int) $nt->fetchColumn() ?> tips</p></div>
 </a>
<?php endforeach; ?>
</div>
<?php admin_foot();
