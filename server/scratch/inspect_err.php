<?php
$dir = 'server/pmmp_src/src/';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($it as $f) {
    if ($f->isFile() && $f->getExtension() === 'php') {
        $c = file_get_contents($f->getPathname());
        if (strpos($c, 'SpawnResponsePacketHandler') !== false) {
            echo $f->getPathname() . "\n";
        }
    }
}
exit;



$files = [];
foreach ($it as $file) {
    if ($file->isFile() && $file->getMTime() > time() - 86400) {
        $files[] = [
            'path' => $file->getPathname(),
            'mtime' => date('Y-m-d H:i:s', $file->getMTime()),
            'size' => $file->getSize()
        ];
    }
}
usort($files, fn($a, $b) => strcmp($b['mtime'], $a['mtime']));
foreach (array_slice($files, 0, 30) as $f) {
    echo "{$f['mtime']} ({$f['size']} B) : {$f['path']}\n";
}




