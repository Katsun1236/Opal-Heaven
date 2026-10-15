<?php
require 'c:/Users/basti/OneDrive/Bureau/Opal Heaven/server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

echo "ProtocolInfo::MINECRAFT_VERSION_NETWORK = " . ProtocolInfo::MINECRAFT_VERSION_NETWORK . "\n";
$url = "https://client.discovery.minecraft-services.net/api/v1.0/discovery/MinecraftPE/builds/" . ProtocolInfo::MINECRAFT_VERSION_NETWORK;
echo "Fetching discovery from $url ...\n";
$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$json = @file_get_contents($url, false, $ctx);
echo "Discovery response: " . ($json ?: "FAILED") . "\n";

$jwksUri = "https://authorization.franchise.minecraft-services.net/.well-known/keys";
echo "Fetching keys from $jwksUri ...\n";
$keysJson = @file_get_contents($jwksUri, false, $ctx);
if ($keysJson) {
    $data = json_decode($keysJson, true);
    echo "Keys count: " . count($data['keys'] ?? []) . "\n";
    foreach ($data['keys'] ?? [] as $k) {
        echo "Key ID: " . $k['kid'] . " (alg: " . ($k['alg'] ?? 'none') . ", kty: " . ($k['kty'] ?? 'none') . ")\n";
        try {
            $derKey = JwtUtils::rsaPublicKeyModExpToDer($k['n'], $k['e']);
            $parsed = JwtUtils::parseDerPublicKey($derKey);
            echo " -> PARSED SUCCESSFULLY!\n";
        } catch (\Throwable $e) {
            echo " -> ERROR PARSING: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "Failed to fetch JWKS.\n";
}
