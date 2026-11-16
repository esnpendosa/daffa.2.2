<?php
/** Kerangka halaman publik: kepala (header + menu) dan kaki (footer + skrip). */
require_once __DIR__ . '/functions.php';

function asset_v($path)
{
    $f = ROOT . '/' . $path;
    return $path . (is_file($f) ? '?v=' . filemtime($f) : '');
}

function nav_link($href, $label, $key, $active)
{
    return '<a href="' . $href . '"' . ($active === $key ? ' aria-current="page"' : '') . '>' . $label . '</a>';
}

/**
 * @param string $title  judul penuh (isi <title>)
 * @param string $desc   meta description
 * @param string $active beranda | tentang | kategori | artikel | kontak
 * @param string $body   kelas tema pada <body> (mis. t-pertanian)
 * @param string $sub    slug kategori yang aktif di submenu
 */
function page_head($title, $desc, $active, $body = '', $sub = '')
{
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#174D32">
<meta name="description" content="<?= e($desc) ?>">
<title><?= e($title) ?></title>
<link rel="icon" href="assets/images/logo-icon.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset_v('css/style.css')) ?>">
</head>
<body<?= $body !== '' ? ' class="' . e($body) . '"' : '' ?>>
<a class="skip" href="#isi">Lompat ke isi</a>
<header class="navbar">
 <div class="nav-inner">
  <a class="brand" href="index.php" aria-label="AgroNusa, ke beranda"><img class="bi" src="assets/images/logo-icon.png" alt="" width="44" height="40"><img class="bw" src="assets/images/logo-wordmark.png" alt="AgroNusa" width="130" height="17"></a>
  <button class="menu-toggle" aria-label="Buka menu" aria-expanded="false" aria-controls="menu"><svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
  <nav class="nav-links" id="menu" aria-label="Navigasi utama"><?= nav_link('index.php', 'Beranda', 'beranda', $active) ?><?= nav_link('tentang.php', 'Tentang', 'tentang', $active) ?><div class="has-sub"><?= nav_link('kategori.php', 'Kategori', 'kategori', $active) ?><div class="sub"><?php foreach (categories() as $c) : ?><a href="<?= e($c['slug']) ?>.php" style="--dot:<?= e($c['color']) ?>"<?= $sub === $c['slug'] ? ' aria-current="page"' : '' ?>><?= e($c['name']) ?></a><?php endforeach; ?></div></div><?= nav_link('artikel.php', 'Artikel', 'artikel', $active) ?><?= nav_link('kontak.php', 'Kontak', 'kontak', $active) ?></nav>
  <a class="btn nav-cta" href="kontak.php">Hubungi Kami</a>
 </div>
</header>
<main id="isi">
<?php
}

function page_foot()
{
    $wa = preg_replace('/\D+/', '', setting('wa_number'));
    ?>
</main>
<footer class="footer">
 <div class="container">
  <div class="footer-grid">
   <div><a class="brand" href="index.php" aria-label="AgroNusa, ke beranda"><img class="bf" src="assets/images/logo-agronusa.png" alt="AgroNusa" width="102" height="84"></a><p style="max-width:340px;margin-top:18px"><?= e(setting('footer_desc')) ?></p></div>
   <div><h4>Kategori</h4><div class="footer-links"><?php foreach (categories() as $c) : ?><a href="<?= e($c['slug']) ?>.php"><?= e($c['name']) ?></a><?php endforeach; ?></div></div>
   <div><h4>Halaman</h4><div class="footer-links"><a href="index.php">Beranda</a><a href="tentang.php">Tentang</a><a href="kategori.php">Kategori</a><a href="artikel.php">Artikel</a><a href="kontak.php">Kontak</a></div></div>
   <div><h4>Hubungi</h4><div class="footer-links"><a href="mailto:<?= e(setting('site_email')) ?>"><?= e(setting('site_email')) ?></a><a href="https://wa.me/<?= e($wa) ?>">WhatsApp</a></div></div>
  </div>
  <div class="copyright"><span>&copy; <?= date('Y') ?> AgroNusa. Seluruh hak dilindungi.</span><span><?= e(setting('footer_note')) ?></span></div>
 </div>
</footer>
<script src="<?= e(asset_v('js/main.js')) ?>"></script>
</body>
</html>
<?php
}

/** Hero halaman dalam (page-hero) dengan jejak halaman. $crumbs: array(array(label, url|null)). */
function page_hero($bgSlug, $crumbs, $h1, $lead)
{
    $h = '<section class="page-hero" style="' . bg($bgSlug) . '"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman">';
    $n = count($crumbs);
    foreach ($crumbs as $i => $c) {
        if ($i > 0) {
            $h .= '<span aria-hidden="true">/</span>';
        }
        $h .= ($i === $n - 1) ? '<span aria-current="page">' . e($c[0]) . '</span>' : '<a href="' . e($c[1]) . '">' . e($c[0]) . '</a>';
    }
    return $h . '</nav><h1>' . e($h1) . '</h1><p class="lead">' . e($lead) . '</p></div></section>';
}

/** Blok ajakan (CTA) sebelum footer. */
function cta_block($title, $text, $label2, $url2)
{
    return '<section class="section tight"><div class="container"><div class="cta rv"><div><h2>' . e($title) . '</h2><p>' . e($text) . '</p></div>'
        . '<div class="cta-actions"><a class="btn btn-lime" href="kontak.php">Hubungi Kami</a><a class="btn btn-ghost" href="' . e($url2) . '">' . e($label2) . '</a></div></div></div></section>';
}
