<?php

require_once __DIR__ . '/../server/pmmp_src/vendor/autoload.php';

use pocketmine\network\mcpe\JwtUtils;

$url = "https://authorization.franchise.minecraft-services.net/.well-known/keys";
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'header' => "User-Agent: PocketMine-MP\r\n"
    ]
]);

$json = file_get_contents($url, false, $context);
if($json === false){
    echo "Failed to fetch keys from $url\n";
    exit(1);
}

$data = json_decode($json, true);
echo "Fetched keys count: " . count($data['keys'] ?? []) . "\n";

foreach($data['keys'] as $k){
    echo "Testing kid: " . $k['kid'] . ", kty: " . $k['kty'] . ", n len: " . strlen($k['n']) . ", e: " . $k['e'] . "\n";
    try {
        $derKey = JwtUtils::rsaPublicKeyModExpToDer($k['n'], $k['e']);
        echo "DER key length: " . strlen($derKey) . "\n";
        $pem = JwtUtils::derPublicKeyToPem($derKey);
        $res = openssl_pkey_get_public($pem);
        if($res === false){
            echo "FAILED to parse: " . openssl_error_string() . "\n";
        } else {
            echo "SUCCESS parsing RSA key!\n";
        }
    } catch(\Throwable $e){
        echo "EXCEPTION: " . $e->getMessage() . "\n";
    }
}
