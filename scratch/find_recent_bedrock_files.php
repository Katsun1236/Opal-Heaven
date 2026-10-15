<?php
$dirs = [
    getenv('APPDATA') . '/Minecraft Bedrock',
    getenv('LOCALAPPDATA') . '/Packages'
];

foreach ($dirs as $base) {
    if (!is_dir($base)) continue;
    echo "Scanning $base ...\n";
    try {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        $count = 0;
        foreach ($it as $file) {
            if ($file->isFile()) {
                $mtime = $file->getMTime();
                if ($mtime > time() - 3600 * 6) { // last 6 hours
                    $name = $file->getFilename();
                    if (preg_match('/\.(txt|log|json|dmp|mdmp)$/i', $name)) {
                        echo date("H:i:s", $mtime) . " [" . $file->getSize() . "b] " . $file->getPathname() . "\n";
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        echo "Error scanning $base: " . $e->getMessage() . "\n";
    }
}
