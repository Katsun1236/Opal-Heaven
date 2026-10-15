<?php
$phar = new Phar('c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/plugins/Customies.phar');
echo $phar['src/CustomiesListener.php']->getContent();
