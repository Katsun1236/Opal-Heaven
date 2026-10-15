<?php
$pharPath = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$extractTo = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src';

if (!is_dir($extractTo)) {
    mkdir($extractTo, 0777, true);
}

echo "Extracting PocketMine-MP.phar...\n";
$phar = new Phar($pharPath);
$phar->extractTo($extractTo, null, true);
echo "Extracted successfully to $extractTo!\n";
