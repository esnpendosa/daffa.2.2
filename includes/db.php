<?php
/** Koneksi database + pemasangan otomatis (tabel dan data awal). */

function db()
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    if (!extension_loaded('pdo_sqlite')) {
        http_response_code(500);
        exit('Ekstensi PHP "pdo_sqlite" belum aktif di hosting ini. Aktifkan lewat pengaturan PHP (cPanel: Select PHP Version > Extensions > pdo_sqlite).');
    }
    $dir = dirname(DB_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    if (!is_writable($dir)) {
        http_response_code(500);
        exit('Folder "data/" harus bisa ditulis oleh server (chmod 755 atau 775).');
    }
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    if ((int) $pdo->query('PRAGMA user_version')->fetchColumn() < 1) {
        $pdo->exec('PRAGMA journal_mode = WAL');
        db_install($pdo);
        $pdo->exec('PRAGMA user_version = 1');
    }
    return $pdo;
}

function db_install(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT NOT NULL UNIQUE,
        name TEXT NOT NULL,
        sort INTEGER NOT NULL DEFAULT 0,
        arch_sort INTEGER NOT NULL DEFAULT 0,
        theme TEXT NOT NULL DEFAULT \'\',
        color TEXT NOT NULL DEFAULT \'#2E8B3A\',
        icon TEXT NOT NULL DEFAULT \'leaf\',
        short_desc TEXT NOT NULL DEFAULT \'\',
        seo_desc TEXT NOT NULL DEFAULT \'\',
        hero_lead TEXT NOT NULL DEFAULT \'\',
        kom_kicker TEXT NOT NULL DEFAULT \'\',
        kom_title TEXT NOT NULL DEFAULT \'\',
        kom_note TEXT NOT NULL DEFAULT \'\',
        tips_kicker TEXT NOT NULL DEFAULT \'\',
        tips_title TEXT NOT NULL DEFAULT \'\',
        tips_intro TEXT NOT NULL DEFAULT \'\',
        tips_soft INTEGER NOT NULL DEFAULT 1,
        compare TEXT,
        show_related INTEGER NOT NULL DEFAULT 1,
        cta_title TEXT NOT NULL DEFAULT \'\',
        cta_text TEXT NOT NULL DEFAULT \'\',
        cta2_label TEXT NOT NULL DEFAULT \'Kategori Lain\',
        cta2_url TEXT NOT NULL DEFAULT \'kategori.php\'
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS commodities (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
        name TEXT NOT NULL,
        icon TEXT NOT NULL DEFAULT \'leaf\',
        description TEXT NOT NULL DEFAULT \'\',
        facts TEXT NOT NULL DEFAULT \'[]\',
        featured INTEGER NOT NULL DEFAULT 0,
        sort INTEGER NOT NULL DEFAULT 0
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS tips (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
        title TEXT NOT NULL,
        text TEXT NOT NULL DEFAULT \'\',
        sort INTEGER NOT NULL DEFAULT 0
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS articles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT NOT NULL UNIQUE,
        category TEXT NOT NULL DEFAULT \'umum\',
        title TEXT NOT NULL,
        excerpt TEXT NOT NULL DEFAULT \'\',
        tips TEXT NOT NULL DEFAULT \'\',
        published_at TEXT NOT NULL,
        status INTEGER NOT NULL DEFAULT 1
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        topic TEXT NOT NULL DEFAULT \'\',
        body TEXT NOT NULL,
        ip TEXT NOT NULL DEFAULT \'\',
        created_at TEXT NOT NULL,
        is_read INTEGER NOT NULL DEFAULT 0
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (k TEXT PRIMARY KEY, v TEXT NOT NULL DEFAULT \'\')');
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at TEXT NOT NULL
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS login_attempts (ip TEXT NOT NULL, ts INTEGER NOT NULL)');

    $n = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    if ($n === 0) {
        db_seed($pdo);
    }
}

/** Isi data awal dari data/seed.json (konten situs lama). */
function db_seed(PDO $pdo)
{
    $file = ROOT . '/data/seed.json';
    if (!is_file($file)) {
        return;
    }
    $seed = json_decode(file_get_contents($file), true);
    if (!is_array($seed)) {
        return;
    }
    $colors = array(
        'pertanian' => '#2E8B3A', 'perkebunan' => '#D9530B', 'peternakan' => '#0E8F87',
        'perikanan' => '#2B6FC4', 'teknologi-agro' => '#6246D6',
    );
    $pdo->beginTransaction();
    $ci = $pdo->prepare('INSERT INTO categories
        (slug,name,sort,arch_sort,theme,color,icon,short_desc,seo_desc,hero_lead,kom_kicker,kom_title,kom_note,
         tips_kicker,tips_title,tips_intro,tips_soft,compare,show_related,cta_title,cta_text,cta2_label,cta2_url)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $ki = $pdo->prepare('INSERT INTO commodities (category_id,name,icon,description,facts,featured,sort) VALUES (?,?,?,?,?,?,?)');
    $ti = $pdo->prepare('INSERT INTO tips (category_id,title,text,sort) VALUES (?,?,?,?)');
    foreach ($seed['categories'] as $c) {
        $compare = !empty($c['compare']) ? json_encode($c['compare'], JSON_UNESCAPED_UNICODE) : null;
        $ci->execute(array(
            $c['slug'], $c['name'], $c['sort'], $c['arch_sort'], $c['theme'],
            isset($colors[$c['slug']]) ? $colors[$c['slug']] : '#2E8B3A', $c['icon'],
            $c['short_desc'], $c['seo_desc'], $c['hero_lead'], $c['kom_kicker'], $c['kom_title'], $c['kom_note'],
            $c['tips_kicker'], $c['tips_title'], $c['tips_intro'], $c['tips_soft'], $compare,
            $c['show_related'], $c['cta_title'], $c['cta_text'], $c['cta2_label'], str_replace('.html', '.php', $c['cta2_url']),
        ));
        $cid = (int) $pdo->lastInsertId();
        foreach ($c['komoditas'] as $k) {
            $ki->execute(array($cid, $k['name'], $k['icon'], $k['description'],
                json_encode($k['facts'], JSON_UNESCAPED_UNICODE), $k['featured'], $k['sort']));
        }
        foreach ($c['tips'] as $t) {
            $ti->execute(array($cid, $t['title'], $t['text'], $t['sort']));
        }
    }
    $ai = $pdo->prepare('INSERT INTO articles (slug,category,title,excerpt,tips,published_at,status) VALUES (?,?,?,?,?,?,?)');
    foreach ($seed['articles'] as $a) {
        $ai->execute(array($a['slug'], $a['category'], $a['title'], $a['excerpt'], $a['tips'], $a['published_at'], $a['status']));
    }
    $pdo->commit();
}
