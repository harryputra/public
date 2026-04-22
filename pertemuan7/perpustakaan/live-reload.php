<?php
/**
 * SmartLib Live Reload Helper
 * Memantau perubahan file untuk melakukan auto-refresh pada browser.
 */

// Folder yang akan diawasi
$watch_dirs = [
    __DIR__,
    __DIR__ . '/admin',
    __DIR__ . '/librarian',
    __DIR__ . '/student',
    __DIR__ . '/includes',
    __DIR__ . '/config',
    __DIR__ . '/assets/css',
    __DIR__ . '/assets/js',
];

$latest_mtime = 0;

foreach ($watch_dirs as $dir) {
    if (!is_dir($dir)) continue;
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if (in_array($ext, ['php', 'css', 'js', 'sql'])) {
                $mtime = $file->getMTime();
                if ($mtime > $latest_mtime) {
                    $latest_mtime = $mtime;
                }
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['last_modified' => $latest_mtime]);
