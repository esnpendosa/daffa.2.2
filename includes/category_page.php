<?php
/** Template halaman kategori. Dipanggil oleh pertanian.php, perkebunan.php, dst. */
require_once __DIR__ . '/layout.php';

function render_category_page($slug)
{
    $c = category($slug);
    if (!$c) {
        http_response_code(404);
        exit('Kategori tidak ditemukan.');
    }
    $kom = commodities_of($c['id']);
    $tips = tips_of($c['id']);
    $cmp = !empty($c['compare']) ? json_decode($c['compare'], true) : null;
    $related = $c['show_related'] ? articles($c['slug']) : array();

    page_head($c['name'] . ' | AgroNusa', $c['seo_desc'], 'kategori', $c['theme'], $c['slug']);
    echo page_hero($c['slug'], array(array('Beranda', 'index.php'), array('Kategori', 'kategori.php'), array($c['name'], null)), $c['name'], $c['hero_lead']);
    ?>
<section class="section"><div class="container"><div class="section-head rv"><span class="kicker"><?= e($c['kom_kicker']) ?></span><h2><?= e($c['kom_title']) ?></h2><p><?= e($c['kom_note']) ?></p></div>
<div class="cards<?= count($kom) === 3 ? ' three' : '' ?>"><?php foreach ($kom as $k) :
    $facts = json_decode($k['facts'], true);
    if (!is_array($facts)) {
        $facts = array();
    }
    ?><article class="card rv<?= $k['featured'] ? ' key' : '' ?>"><div class="icon-box"><img src="assets/icons/<?= e($k['icon']) ?>.svg" alt=""></div><h3><?= e($k['name']) ?></h3><p><?= e($k['description']) ?></p><dl class="facts"><?php foreach ($facts as $f) : ?><div><dt><?= e($f[0]) ?></dt><dd><?= e($f[1]) ?></dd></div><?php endforeach; ?></dl></article><?php endforeach; ?></div></div></section>
<?php if ($cmp && !empty($cmp['rows'])) : ?>
<section class="section soft"><div class="container"><div class="section-head rv"><span class="kicker"><?= e($cmp['kicker']) ?></span><h2><?= e($cmp['title']) ?></h2></div>
<div class="table-wrap rv"><table><thead><tr><?php foreach ($cmp['rows'][0] as $th) : ?><th><?= e($th) ?></th><?php endforeach; ?></tr></thead><tbody><?php foreach (array_slice($cmp['rows'], 1) as $tr) : ?><tr><?php foreach ($tr as $td) : ?><td><?= e($td) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table></div>
<?php if (!empty($cmp['note'])) : ?><p class="note" style="margin-top:14px"><?= e($cmp['note']) ?></p><?php endif; ?></div></section>
<?php endif; ?>
<?php if ($tips) : ?>
<section class="section<?= ($cmp && !empty($cmp['rows'])) ? '' : ($c['tips_soft'] ? ' soft' : '') ?>"><div class="container two">
<div class="rv"><span class="kicker"><?= e($c['tips_kicker']) ?></span><h2><?= e($c['tips_title']) ?></h2><p><?= e($c['tips_intro']) ?></p></div>
<ol class="steps rv"><?php foreach ($tips as $t) : ?><li><div><strong><?= e($t['title']) ?></strong><span><?= e($t['text']) ?></span></div></li><?php endforeach; ?></ol></div></section>
<?php endif; ?>
<?php if ($related) : ?>
<section class="section tight"><div class="container"><div class="section-head rv"><span class="kicker">Artikel terkait</span><h2>Baca lebih lanjut.</h2></div>
<div class="arts"<?= count($related) === 1 ? ' style="grid-template-columns:minmax(0,420px);justify-content:center"' : '' ?>><?php foreach ($related as $a) { echo render_article_card($a, 'link'); } ?></div></div></section>
<?php endif; ?>
<?= cta_block($c['cta_title'], $c['cta_text'], $c['cta2_label'], $c['cta2_url']) ?>
<?php
    page_foot();
}
