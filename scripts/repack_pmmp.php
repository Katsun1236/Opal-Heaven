<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$backupFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar.bak';
$src = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src';
$stub = file_get_contents('c:/Users/basti/OneDrive/Bureau/Opal Heaven/scripts/pmmp_stub.php');

if (file_exists($pharFile)) {
    if (!file_exists($backupFile)) {
        copy($pharFile, $backupFile);
    }
    unlink($pharFile);
}

echo "Repacking PocketMine-MP.phar with protocol patch...\n";

$phar = new Phar($pharFile);
$phar->startBuffering();
$phar->buildFromDirectory($src);
$phar->setStub($stub);
$phar->stopBuffering();

echo "Repacked successfully! Size: " . filesize($pharFile) . " bytes\n";
