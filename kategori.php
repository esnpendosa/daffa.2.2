<?php
require __DIR__ . '/includes/layout.php';
page_head('Kategori | AgroNusa', 'Ringkasan lima kategori AgroNusa: pertanian, perkebunan, peternakan, perikanan, dan teknologi agro.', 'kategori');
echo page_hero('umum', array(array('Beranda', 'index.php'), array('Kategori', null)), 'Lima kategori, satu portal.', 'Ringkasan setiap bidang agro. Pilih satu untuk melihat komoditas dan tips lengkapnya.');
?>
<section class="section"><div class="container">
<?php foreach (categories() as $i => $c) :
    $names = db()->prepare('SELECT name FROM commodities WHERE category_id = ? ORDER BY sort, id');
    $names->execute(array($c['id']));
    ?><div class="row rv<?= $i % 2 === 1 ? ' rev' : '' ?> <?= e($c['theme']) ?>">
<div class="pic" style="<?= bg($c['slug']) ?>" role="img" aria-label="Ilustrasi <?= e($c['name']) ?>"></div>
<div><h2><span class="cat-ico"><img src="assets/icons/<?= e($c['icon']) ?>.svg" alt="" width="28" height="28"></span><?= e($c['name']) ?></h2><p><?= e($c['short_desc']) ?></p>
<div class="chips"><?php foreach ($names->fetchAll() as $n) : ?><span><?= e($n['name']) ?></span><?php endforeach; ?></div>
<p style="margin-top:18px"><a class="btn" href="<?= e($c['slug']) ?>.php">Buka <?= e($c['name']) ?></a></p></div></div>
<?php endforeach; ?>
</div></section>
<?= cta_block('Belum yakin mulai dari mana?', 'Ceritakan kondisi lahan atau minat Anda, kami bantu mengarahkan.', 'Baca Artikel', 'artikel.php') ?>
<?php page_foot();
