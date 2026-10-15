<?php
$base = getenv('APPDATA') . '/Minecraft Bedrock';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
$found = [];
foreach ($iterator as $file) {
    if ($file->isFile() && in_array(strtolower($file->getExtension()), ['txt', 'log'])) {
        $found[] = [
            'path' => $file->getPathname(),
            'mtime' => $file->getMTime(),
            'size' => $file->getSize()
        ];
    }
}
usort($found, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
foreach (array_slice($found, 0, 15) as $f) {
    echo date("Y-m-d H:i:s", $f['mtime']) . " (" . $f['size'] . "b): " . $f['path'] . "\n";
}
