<?php
declare(strict_types=1);

$plugins = ['OpalCore', 'OpalMod'];
foreach ($plugins as $name) {
    $dir = __DIR__ . '/../plugins/' . $name;
    $pharFile = __DIR__ . '/../plugins/' . $name . '.phar';
    if (file_exists($pharFile)) {
        unlink($pharFile);
    }
    $phar = new Phar($pharFile);
    $phar->buildFromDirectory($dir);
    echo "Built {$name}.phar successfully (" . filesize($pharFile) . " bytes)\n";
}
