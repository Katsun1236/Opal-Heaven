<?php
$p = new Phar('c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar');
foreach (new DirectoryIterator('phar://' . str_replace('\\', '/', $p->getPath())) as $f) {
    echo $f->getFilename() . "\n";
}
