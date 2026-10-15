<?php

$cutoff = strtotime("2026-10-15 00:00:00");
$dirs = [
    __DIR__ . '/../server/pmmp_src/src',
    __DIR__ . '/../server/pmmp_src/vendor/pocketmine'
];

$files = [];
foreach($dirs as $d){
    if(!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
    foreach ($it as $file) {
        if ($file->isFile() && $file->getMTime() > $cutoff) {
            $files[] = [
                'time' => date('Y-m-d H:i:s', $file->getMTime()),
                'path' => $file->getPathname()
            ];
        }
    }
}

usort($files, fn($a, $b) => strcmp($b['time'], $a['time']));

echo "Total modified files in last 48 hours: " . count($files) . "\n";
foreach ($files as $f) {
    echo "{$f['time']} | {$f['path']}\n";
}

