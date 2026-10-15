<?php
$plugins = [
    'OpalCore' => __DIR__ . '/../server/plugins/OpalCore',
    'OpalMod' => __DIR__ . '/../server/plugins/OpalMod'
];

foreach ($plugins as $name => $srcDir) {
    $pharFile = __DIR__ . "/../server/plugins/{$name}.phar";
    if (file_exists($pharFile)) {
        unlink($pharFile);
    }
    
    $phar = new Phar($pharFile);
    $phar->startBuffering();
    $phar->buildFromDirectory($srcDir);
    $phar->stopBuffering();
    
    echo "Phar cree avec succes: {$name}.phar (" . filesize($pharFile) . " octets)\n";
}
