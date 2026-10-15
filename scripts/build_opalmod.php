<?php

$src = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugins/OpalMod';
$dest = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugins/OpalMod.phar';

if (file_exists($dest)) {
    unlink($dest);
}

echo "Compilation de OpalMod en .phar...\n";

$phar = new Phar($dest);
$phar->startBuffering();
$phar->buildFromDirectory($src);
$phar->setStub('<?php __HALT_COMPILER();');
$phar->stopBuffering();

echo "OpalMod.phar genere avec succes ! (" . filesize($dest) . " octets)\n";
