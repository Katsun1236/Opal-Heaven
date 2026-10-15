<?php
$pharFile = 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('phar://' . $pharFile));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $c = file_get_contents($file->getPathname());
        if (strpos($c, 'disconnectIncompatibleProtocol') !== false) {
            echo $file->getPathname() . "\n";
            $lines = explode("\n", $c);
            foreach ($lines as $i => $l) {
                if (strpos($l, 'disconnectIncompatibleProtocol') !== false) {
                    echo "  Line " . ($i + 1) . ": " . trim($l) . "\n";
                }
            }
        }
    }
}
