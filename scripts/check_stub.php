<?php
$phar = new Phar('c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar');
echo substr($phar->getStub(), 0, 500) . "\n";
