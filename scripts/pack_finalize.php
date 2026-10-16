<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$packDir = $root . '/server/resource_packs/OpalPack';

// Update manifest.json version
$manifest = json_decode(file_get_contents($packDir . '/manifest.json'), true);
$manifest['header']['version'] = [1, 1, 0];
$manifest['modules'][0]['version'] = [1, 1, 0];
file_put_contents($packDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Compress
$zip1 = $root . '/server/resource_packs/OpalPack.zip';
$zip2 = $root . '/server_hub/resource_packs/OpalPack.zip';

if (file_exists($zip1)) @unlink($zip1);
if (file_exists($zip2)) @unlink($zip2);

$cmd = 'powershell -NoProfile -ExecutionPolicy Bypass -Command "Compress-Archive -Path \'' . $packDir . '/*\' -DestinationPath \'' . $zip1 . '\' -Force"';
exec($cmd, $out, $ret);

if (file_exists($zip1)) {
    copy($zip1, $zip2);
    echo "Archive OpalPack.zip créée (" . round(filesize($zip1) / 1024, 1) . " KB) et synchronisée dans server et server_hub.\n";
} else {
    echo "Erreur compression !\n";
}

// resource_packs.yml
$ymlContent = <<<YAML
# Opal Heaven Resource Packs Configuration
force_resources: true
resource_stack:
  - "OpalPack.zip"

YAML;

file_put_contents($root . '/server/resource_packs/resource_packs.yml', $ymlContent);
file_put_contents($root . '/server_hub/resource_packs/resource_packs.yml', $ymlContent);
echo "resource_packs.yml configuré avec force_resources: true et resource_stack: [\"OpalPack.zip\"] dans server & server_hub.\n";
