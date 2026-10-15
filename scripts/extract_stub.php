<?php
$phar = new Phar('c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/PocketMine-MP.phar');
file_put_contents('c:/Users/basti/OneDrive/Bureau/Opal Heaven/scripts/pmmp_stub.php', $phar->getStub());
echo "Stub extracted! Size: " . strlen($phar->getStub()) . " bytes\n";
