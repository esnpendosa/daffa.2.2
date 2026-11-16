<?php
/** Fungsi bantu yang dipakai halaman publik dan admin. */
require_once __DIR__ . '/../config.php';
require_once ROOT . '/includes/db.php';

/* ---------- Umum ---------- */

function e($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function start_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_name('agronusa_sid');
        session_set_cookie_params(array(
            'lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax',
        ));
        session_start();
    }
}

function csrf_token()
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check()
{
    start_session();
    $t = isset($_POST['_csrf']) ? (string) $_POST['_csrf'] : '';
    if ($t === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) {
        http_response_code(400);
        exit('Sesi tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

function post($k, $d = '')
{
    return isset($_POST[$k]) && !is_array($_POST[$k]) ? trim((string) $_POST[$k]) : $d;
}

function client_ip()
{
    return isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : '';
}

/* ---------- Pengaturan situs ---------- */

function setting_defaults()
{
    return array(
        'site_email' => 'halo@agronusa.id',
        'wa_number' => '6280000000000',
        'wa_display' => '+62 800-0000-0000',
        'instagram' => '#',
        'youtube' => '#',
        'facebook' => '#',
        'tiktok' => '#',
        'hero_eyebrow' => 'Portal informasi agro Nusantara',
        'hero_title' => 'Dari tanah, air, dan teknologi untuk Nusantara.',
        'hero_lead' => 'AgroNusa merangkum dasar budidaya pertanian, perkebunan, peternakan, perikanan, dan teknologi agro dalam bahasa yang mudah dipakai di lapangan.',
        'footer_desc' => 'Portal informasi agro Nusantara: pertanian, perkebunan, peternakan, perikanan, dan teknologi agro.',
        'footer_note' => 'Informasi bersifat umum; sesuaikan dengan kondisi lahan dan arahan penyuluh setempat.',
        'notify_mail' => '0',
    );
}

function settings()
{
    static $c = null;
    if ($c === null) {
        $c = setting_defaults();
        foreach (db()->query('SELECT k, v FROM settings') as $r) {
            $c[$r['k']] = $r['v'];
        }
    }
    return $c;
}

function setting($k, $d = '')
{
    $s = settings();
    return isset($s[$k]) ? $s[$k] : $d;
}

function save_setting($k, $v)
{
    $st = db()->prepare('INSERT OR REPLACE INTO settings (k, v) VALUES (?, ?)');
    $st->execute(array($k, $v));
}

/* ---------- Konten ---------- */

function categories()
{
    static $c = null;
    if ($c === null) {
        $c = db()->query('SELECT * FROM categories ORDER BY sort, id')->fetchAll();
    }
    return $c;
}

function category($slug)
{
    foreach (categories() as $c) {
        if ($c['slug'] === $slug) {
            return $c;
        }
    }
    return null;
}

function category_by_id($id)
{
    foreach (categories() as $c) {
        if ((int) $c['id'] === (int) $id) {
            return $c;
        }
    }
    return null;
}

function commodities_of($catId)
{
    $st = db()->prepare('SELECT * FROM commodities WHERE category_id = ? ORDER BY sort, id');
    $st->execute(array($catId));
    return $st->fetchAll();
}

function tips_of($catId)
{
    $st = db()->prepare('SELECT * FROM tips WHERE category_id = ? ORDER BY sort, id');
    $st->execute(array($catId));
    return $st->fetchAll();
}

/** Artikel terbit, terbaru dulu. */
function articles($cat = null, $limit = 0)
{
    $sql = 'SELECT * FROM articles WHERE status = 1';
    $args = array();
    if ($cat !== null) {
        $sql .= ' AND category = ?';
        $args[] = $cat;
    }
    $sql .= ' ORDER BY published_at DESC, id DESC';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int) $limit;
    }
    $st = db()->prepare($sql);
    $st->execute($args);
    return $st->fetchAll();
}

function category_label($slug)
{
    if ($slug === 'umum') {
        return 'Umum';
    }
    $c = category($slug);
    return $c ? $c['name'] : ucfirst($slug);
}

/** Daftar pilihan kategori artikel: semua kategori + "umum". */
function article_category_options()
{
    $o = array();
    foreach (categories() as $c) {
        $o[$c['slug']] = $c['name'];
    }
    $o['umum'] = 'Umum';
    return $o;
}

function icon_list()
{
    $l = array();
    foreach (glob(ROOT . '/assets/icons/*.svg') as $f) {
        $l[] = basename($f, '.svg');
    }
    sort($l);
    return $l;
}

function slugify($s)
{
    $s = mb_strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
    return trim($s, '-');
}

/* ---------- Gambar ---------- */

/**
 * Alamat gambar banner. Jika ada foto asli di assets/photos/<slug>.(jpg|png|webp)
 * foto itu dipakai menggantikan ilustrasi SVG.
 * $ctx 'css' = untuk --bg di dalam atribut style (relatif terhadap css/), 'img' = untuk <img src>.
 */
function banner($slug, $ctx = 'css')
{
    static $cache = array();
    if (!isset($cache[$slug])) {
        $cache[$slug] = 'assets/images/banner-' . $slug . '.svg';
        foreach (array('jpg', 'jpeg', 'png', 'webp') as $x) {
            $p = 'assets/photos/' . $slug . '.' . $x;
            if (is_file(ROOT . '/' . $p)) {
                $cache[$slug] = $p . '?v=' . filemtime(ROOT . '/' . $p);
                break;
            }
        }
    }
    return $ctx === 'css' ? '../' . $cache[$slug] : $cache[$slug];
}

function bg($slug)
{
    return "--bg:url('" . banner($slug, 'css') . "')";
}

/* ---------- Tanggal ---------- */

function tgl_id($date)
{
    $bulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $t = strtotime($date);
    if ($t === false) {
        return (string) $date;
    }
    return (int) date('j', $t) . ' ' . $bulan[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
}

function tgl_waktu_id($datetime)
{
    $t = strtotime($datetime);
    return $t === false ? (string) $datetime : tgl_id($datetime) . ', ' . date('H.i', $t);
}

/* ---------- Pemakaian ulang potongan HTML ---------- */

/** Satu kartu artikel. $mode 'full' = dengan <details> tips (halaman artikel), 'link' = dengan tautan "Baca tips". */
function render_article_card($a, $mode)
{
    $h = '<article class="art rv" id="' . e($a['slug']) . '"' . ($mode === 'full' ? ' data-cat="' . e($a['category']) . '"' : '') . '>';
    $h .= '<div class="art-thumb" style="' . bg($a['category']) . '"><span class="tag">' . e(category_label($a['category'])) . '</span></div>';
    $h .= '<div class="art-body"><time datetime="' . e($a['published_at']) . '">' . e(tgl_id($a['published_at'])) . '</time>';
    $h .= '<h3>' . e($a['title']) . '</h3><p>' . e($a['excerpt']) . '</p>';
    if ($mode === 'full') {
        $lines = preg_split('/\r\n|\r|\n/', (string) $a['tips']);
        $li = '';
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln !== '') {
                $li .= '<li>' . e($ln) . '</li>';
            }
        }
        if ($li !== '') {
            $h .= '<details><summary>Lihat tips</summary><ul>' . $li . '</ul></details>';
        }
    } else {
        $h .= '<a class="go" href="artikel.php#' . e($a['slug']) . '">Baca tips →</a>';
    }
    return $h . '</div></article>';
}
