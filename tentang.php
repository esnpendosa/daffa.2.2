<?php
require __DIR__ . '/includes/layout.php';
page_head('Tentang | AgroNusa', 'Visi, misi, dan alasan di balik nama AgroNusa.', 'tentang');
echo page_hero('umum', array(array('Beranda', 'index.php'), array('Tentang', null)), 'Tentang AgroNusa', 'Visi, misi, dan alasan di balik nama kami.');
?>
<section class="section"><div class="container two">
<div class="rv"><span class="kicker">Siapa kami</span><h2>Informasi agro yang dekat dengan pekerjaan di lapangan.</h2><p>AgroNusa lahir dari gagasan sederhana: pengetahuan dasar budidaya harus mudah ditemukan dan mudah dipahami oleh petani, peternak, pembudidaya ikan, pelajar, dan siapa pun yang ingin mulai.</p><p>Kami menyusun ringkasan per komoditas, tips praktis, dan pengenalan teknologi agro dengan bahasa sehari-hari.</p></div>
<div class="pic rv" style="<?= bg('pertanian') ?>" role="img" aria-label="Ilustrasi hamparan sawah menguning"></div></div></section>
<section class="section soft"><div class="container"><div class="section-head rv"><span class="kicker">Arah kami</span><h2>Visi dan misi.</h2></div>
 <div class="cards three"><article class="card rv key"><div class="icon-box"><img src="assets/icons/leaf.svg" alt=""></div><h3>Visi</h3><p>Menjadi rujukan informasi agro berbahasa Indonesia yang mudah dipahami dan bermanfaat bagi pelaku usaha tani dari hulu ke hilir.</p></article>
 <article class="card rv wide"><h3>Misi</h3><ul class="check" style="margin-bottom:0"><li>Menyajikan panduan dasar lima bidang agro dengan ringkas dan akurat.</li><li>Memperkenalkan teknologi agro yang terjangkau dan realistis dipakai.</li><li>Mendorong kebiasaan mencatat dan berbudidaya secara berkelanjutan.</li><li>Menjadi pintu awal sebelum petani berkonsultasi dengan penyuluh setempat.</li></ul></article></div></div></section>
<section class="section"><div class="container two">
<div class="pic rv" style="<?= bg('beranda') ?>" role="img" aria-label="Ilustrasi terasering sawah dan pohon kelapa saat fajar"></div>
<div class="rv"><span class="kicker">Arti nama</span><h2>Agro + Nusa = agro dari seluruh Nusantara.</h2><p><strong>Agro</strong> merujuk pada segala hal tentang pertanian dalam arti luas. <strong>Nusa</strong> berarti pulau; <strong>Nusantara</strong> adalah gugusan pulau Indonesia dengan tanah, iklim, dan komoditas yang sangat beragam.</p><p>Nama ini mengingatkan kami bahwa tidak ada satu resep yang cocok untuk semua daerah. Karena itu kami selalu menulis angka sebagai rentang umum.</p></div></div></section>
<section class="section soft"><div class="container"><div class="section-head rv"><span class="kicker">Nilai</span><h2>Cara kami bekerja.</h2></div>
<div class="pillars"><div class="pillar rv"><b>A</b><h3>Mudah dipahami</h3><p>Bahasa sehari-hari, kalimat pendek, contoh nyata.</p></div><div class="pillar rv"><b>B</b><h3>Berimbang</h3><p>Menyebut keterbatasan informasi dan menganjurkan konsultasi lokal.</p></div><div class="pillar rv"><b>C</b><h3>Terus diperbarui</h3><p>Artikel dan panduan ditambah dan diperbaiki berkala.</p></div></div></div></section>
<?= cta_block('Mau bertanya atau memberi masukan?', 'Kami senang mendengar dari Anda.', 'Lihat Kategori', 'kategori.php') ?>
<?php page_foot();
