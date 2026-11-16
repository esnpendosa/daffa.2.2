<?php
require __DIR__ . '/includes/layout.php';
$cats = categories();
$archs = $cats;
usort($archs, function ($a, $b) { return (int) $a['arch_sort'] - (int) $b['arch_sort']; });
$nKom = (int) db()->query('SELECT COUNT(*) FROM commodities')->fetchColumn();
$nArt = (int) db()->query('SELECT COUNT(*) FROM articles WHERE status = 1')->fetchColumn();

page_head('AgroNusa | Portal informasi pertanian, perkebunan, peternakan, dan perikanan',
    'AgroNusa: portal informasi agro Nusantara. Panduan dasar pertanian, perkebunan, peternakan, perikanan, dan teknologi agro.',
    'beranda');
?>
<section class="hero"><div class="container" style="display:flex;flex-direction:column;align-items:center">
 <span class="eyebrow"><?= e(setting('hero_eyebrow')) ?></span>
 <h1><?= e(setting('hero_title')) ?></h1>
 <p class="lead"><?= e(setting('hero_lead')) ?></p>
 <div class="hero-actions"><a class="btn btn-lime btn-lg" href="kategori.php">Jelajahi Kategori</a><a class="btn btn-ghost btn-lg" href="artikel.php">Baca Artikel</a></div></div>
 <div class="arches"><?php foreach ($archs as $c) : ?><a class="arch" href="<?= e($c['slug']) ?>.php" style="<?= bg($c['slug']) ?>" aria-label="<?= e($c['name']) ?>"><span><img src="assets/icons/<?= e($c['icon']) ?>.svg" alt=""><?= e($c['name']) ?></span></a><?php endforeach; ?></div></section>
<section class="section soft" id="kategori"><div class="container"><div class="section-head rv"><span class="kicker">Kategori</span><h2>Jelajahi bidang agro, pilih yang ingin Anda pelajari.</h2><p>Lima kategori, masing-masing berisi komoditas utama dan tips budidaya.</p></div>
 <div class="svc rv"><div class="svc-pic"><?php foreach ($cats as $i => $c) : ?><img class="<?= $i === 0 ? 'on' : '' ?>" src="<?= e(banner($c['slug'], 'img')) ?>" alt="Ilustrasi <?= e($c['name']) ?>"<?= $i > 0 ? ' loading="lazy" decoding="async"' : '' ?>><?php endforeach; ?></div><div class="acc"><?php foreach ($cats as $i => $c) :
    $names = db()->prepare('SELECT name FROM commodities WHERE category_id = ? ORDER BY sort, id');
    $names->execute(array($c['id']));
    ?><div class="acc-i<?= $i === 0 ? ' open' : '' ?>" style="--c:<?= e($c['color']) ?>"><button type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" data-img="<?= $i ?>"><img src="assets/icons/<?= e($c['icon']) ?>.svg" alt="" width="34" height="34"><?= e($c['name']) ?></button><div class="acc-p"><p style="margin:0"><?= e($c['short_desc']) ?></p><div class="chips"><?php foreach ($names->fetchAll() as $n) : ?><span><?= e($n['name']) ?></span><?php endforeach; ?></div><a href="<?= e($c['slug']) ?>.php">Jelajahi <?= e($c['name']) ?> →</a></div></div><?php endforeach; ?></div></div></div></section>
<section class="section"><div class="container two">
 <div class="rv"><span class="kicker">Agro dalam angka</span><h2>Menyederhanakan dunia agro untuk semua.</h2><p>Semua materi disusun ringkas: penjelasan singkat, fakta kunci, dan tips yang bisa langsung dicoba di lahan, kandang, atau kolam Anda.</p><a class="btn" href="kategori.php">Lihat Semua Kategori</a></div>
 <div class="stats rv"><div class="stat"><b><?= count($cats) ?></b><span>Kategori agro</span></div><div class="stat"><b><?= $nKom ?></b><span>Komoditas dan topik</span></div><div class="stat"><b>3</b><span>Fakta kunci tiap komoditas</span></div><div class="stat"><b><?= $nArt ?></b><span>Artikel tips budidaya</span></div></div></div></section>
<section class="section soft" id="apa-itu-agro"><div class="container two">
 <div class="pic-wrap rv"><div class="pic arch-pic" style="<?= bg('umum') ?>" role="img" aria-label="Ilustrasi perbukitan hijau di pagi hari"></div><a class="pic-badge" href="mailto:<?= e(setting('site_email')) ?>"><img src="assets/icons/mail.svg" alt="" width="22" height="22"><span>Tanya kami<br><b><?= e(setting('site_email')) ?></b></span></a></div>
 <div class="rv"><span class="kicker">Apa itu agro?</span><h2>Agro adalah seluruh rantai kehidupan dari lahan dan perairan.</h2>
 <p>Kata agro berkaitan dengan pertanian dalam arti luas: menanam tanaman, merawat kebun, beternak, membudidayakan ikan, sampai memanfaatkan teknologi agar kerja lebih ringan dan hasil lebih baik.</p>
 <ul class="check"><li>Penjelasan singkat dan praktis, tanpa istilah berbelit</li><li>Fakta dasar tiap komoditas: masa panen, kondisi ideal, tips</li><li>Berlaku umum; selalu cocokkan dengan kondisi daerah Anda</li></ul>
 <a class="btn" href="tentang.php">Kenali AgroNusa</a></div></div></section>
<section class="section dark"><div class="container two">
 <div class="rv"><span class="kicker" style="background:rgba(255,255,255,.14);color:#fff">Teknologi agro</span><h2 style="color:#fff">Kerja lebih efisien dengan teknologi yang tepat.</h2><p style="color:#B8CABB">Hidroponik untuk lahan sempit, sensor dan irigasi otomatis untuk memantau lahan, serta drone untuk menyemprot dan memetakan area luas.</p><a class="btn btn-lime" href="teknologi-agro.php">Lihat teknologi agro</a></div>
 <div class="pic rv" style="<?= bg('teknologi-agro') ?>" role="img" aria-label="Ilustrasi drone di atas lahan"></div></div></section>
<section class="section"><div class="container"><div class="big2">
 <div class="big rv" style="<?= bg('pertanian') ?>"><h3>Baca panduan dan tips lapangan</h3><p>Dari membaca pH tanah sampai biosekuriti kandang, semua ada di artikel kami.</p><a class="btn btn-lime" href="artikel.php">Semua Artikel</a></div>
 <div class="big rv" style="<?= bg('perikanan') ?>"><h3>Ada pertanyaan seputar agro?</h3><p>Tulis pertanyaan Anda, kami bantu arahkan ke informasi yang tepat.</p><a class="btn btn-lime" href="kontak.php">Hubungi Kami</a></div></div></div></section>
<?php $latest = articles(null, 3); if ($latest) : ?>
<section class="section soft"><div class="container"><div class="section-head rv"><span class="kicker">Artikel terbaru</span><h2>Tips budidaya minggu ini.</h2></div><div class="arts"><?php foreach ($latest as $a) { echo render_article_card($a, 'link'); } ?></div><p style="text-align:center;margin-top:34px"><a class="btn btn-outline" href="artikel.php">Semua artikel</a></p></div></section>
<?php endif; ?>
<?php page_foot();
