<?php
require __DIR__ . '/includes/layout.php';
page_head('Artikel | AgroNusa', 'Artikel dan tips budidaya pertanian, perkebunan, peternakan, perikanan, dan teknologi agro.', 'artikel');
echo page_hero('umum', array(array('Beranda', 'index.php'), array('Artikel', null)), 'Artikel dan tips budidaya.', 'Bacaan singkat yang bisa langsung dipraktikkan.');
$arts = articles();
?>
<section class="section"><div class="container"><div class="filters" role="group" aria-label="Filter kategori artikel"><button type="button" data-filter="semua" aria-pressed="true">Semua</button><?php foreach (article_category_options() as $slug => $name) : ?><button type="button" data-filter="<?= e($slug) ?>" aria-pressed="false"><?= e($name) ?></button><?php endforeach; ?></div>
<div class="arts"><?php foreach ($arts as $a) { echo render_article_card($a, 'full'); } ?></div>
<?php if (!$arts) : ?><p class="note" style="text-align:center">Belum ada artikel.</p><?php endif; ?>
</div></section>
<?= cta_block('Punya tips untuk dibagikan?', 'Kirim pengalaman Anda; bisa jadi artikel berikutnya.', 'Baca Artikel', 'artikel.php') ?>
<?php page_foot();
