<?php
if (!defined('ROOT')) {
    define('ROOT', __DIR__);
    define('DB_FILE', ROOT . '/data/agronusa.sqlite');
    define('PHOTO_DIR', ROOT . '/assets/photos');
    define('MAX_UPLOAD_BYTES', 3 * 1024 * 1024); // 3 MB

    date_default_timezone_set('Asia/Jakarta');
    mb_internal_encoding('UTF-8');
}