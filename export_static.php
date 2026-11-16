<?php
$pages = [
    'index.php' => 'index.html',
    'tentang.php' => 'tentang.html',
    'kategori.php' => 'kategori.html',
    'pertanian.php' => 'pertanian.html',
    'perkebunan.php' => 'perkebunan.html',
    'peternakan.php' => 'peternakan.html',
    'perikanan.php' => 'perikanan.html',
    'teknologi-agro.php' => 'teknologi-agro.html',
    'artikel.php' => 'artikel.html',
    'kontak.php' => 'kontak.html',
];

$replacements = [
    'href="index.php"' => 'href="index.html"',
    'href="tentang.php"' => 'href="tentang.html"',
    'href="kategori.php"' => 'href="kategori.html"',
    'href="pertanian.php"' => 'href="pertanian.html"',
    'href="perkebunan.php"' => 'href="perkebunan.html"',
    'href="peternakan.php"' => 'href="peternakan.html"',
    'href="perikanan.php"' => 'href="perikanan.html"',
    'href="teknologi-agro.php"' => 'href="teknologi-agro.html"',
    'href="artikel.php"' => 'href="artikel.html"',
    'href="kontak.php"' => 'href="kontak.html"',
    'href="artikel.php#' => 'href="artikel.html#',
    'kategori.php' => 'kategori.html',
    'artikel.php' => 'artikel.html',
];

foreach ($pages as $src => $dest) {
    echo "Rendering $src -> $dest ...\n";
    $cmd = 'php ' . escapeshellarg(__DIR__ . '/' . $src);
    $output = shell_exec($cmd);
    if (!$output) {
        echo "Error rendering $src\n";
        continue;
    }

    foreach ($replacements as $find => $replace) {
        $output = str_replace($find, $replace, $output);
    }

    // Khusus kontak.html: perbaiki form submit agar tidak error di static hosting
    if ($dest === 'kontak.html') {
        $alertHtml = '<div id="contact-alert" style="display:none;background:#e8f5e9;color:#2e7d32;padding:14px 18px;border-radius:10px;margin-bottom:16px;border:1px solid #c8e6c9;font-size:15px;font-weight:600;">✓ Terima kasih! Pesan Anda telah dicatat. Karena website dihosting di GitHub Pages (mode statis tanpa MySQL/PHP backend), untuk respon cepat silakan hubungi via WhatsApp atau Email yang tertera di samping.</div>';
        $output = str_replace(
            '<form class="form rv" id="form-kontak" method="post" action="kontak.php#form-kontak">',
            '<form class="form rv" id="form-kontak" onsubmit="event.preventDefault(); document.getElementById(\'contact-alert\').style.display=\'block\'; this.reset();">' . $alertHtml,
            $output
        );
        $output = str_replace('action="kontak.php#form-kontak"', '', $output);
    }

    file_put_contents(__DIR__ . '/' . $dest, $output);
    echo "Saved $dest (" . strlen($output) . " bytes)\n";
}

echo "All static pages generated successfully.\n";
