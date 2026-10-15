<?php

$src = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugins/OpalCore';
$dest = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugins/OpalCore.phar';

if (file_exists($dest)) {
    unlink($dest);
}

echo "Compilation de OpalCore en .phar...\n";

$phar = new Phar($dest);
$phar->startBuffering();
$phar->buildFromDirectory($src);
$phar->setStub('<?php __HALT_COMPILER();');
$phar->stopBuffering();

echo "OpalCore.phar genere avec succes ! (" . filesize($dest) . " octets)\n";
