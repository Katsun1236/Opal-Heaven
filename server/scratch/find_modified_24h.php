<?php

$dir = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$recent = [];
$cutoff = time() - (24 * 3600);

foreach ($files as $f) {
    if ($f->isDir()) continue;
    $path = $f->getPathname();
    if (str_contains($path, '.git') || str_contains($path, 'scratch')) continue;
    if ($f->getMTime() > $cutoff) {
        $recent[] = [
            'time' => date('Y-m-d H:i:s', $f->getMTime()),
            'mtime' => $f->getMTime(),
            'size' => $f->getSize(),
            'path' => str_replace('\\', '/', $path)
        ];
    }
}

usort($recent, fn($a, $b) => $b['mtime'] <=> $a['mtime']);

echo "=== FICHIERS MODIFIES CES DERNIERES 24 HEURES ===\n";
foreach (array_slice($recent, 0, 50) as $r) {
    echo "{$r['time']} | " . sprintf("%8d", $r['size']) . " | {$r['path']}\n";
}
