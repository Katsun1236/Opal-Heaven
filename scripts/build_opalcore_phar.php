<?php

declare(strict_types=1);

$source = dirname(__DIR__) . '/plugins_src/OpalCore';
$destinations = [
    dirname(__DIR__) . '/server/plugins/OpalCore.phar',
    dirname(__DIR__) . '/server_hub/plugins/OpalCore.phar'
];

foreach ($destinations as $dest) {
    if (file_exists($dest)) {
        @unlink($dest);
    }
    
    $phar = new Phar($dest);
    $phar->buildFromDirectory($source);
    $phar->setStub('<?php __HALT_COMPILER();');
    echo "Built " . basename($dest) . " successfully (" . round(filesize($dest) / 1024, 1) . " KB).\n";
}
