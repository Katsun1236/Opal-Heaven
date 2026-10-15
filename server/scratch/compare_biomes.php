<?php

$serverBiomes = json_decode(file_get_contents('pmmp_src/vendor/pocketmine/bedrock-data/biome_definitions.json'), true);
echo "Server biomes count: " . count($serverBiomes) . "\n";

require 'scratch/extract_all_client_biomes.php';

$clientKeys = array_keys($allBiomes);
$serverKeys = array_keys($serverBiomes);

$missingInServer = array_diff($clientKeys, $serverKeys);
$missingInClient = array_diff($serverKeys, $clientKeys);

echo "\nMissing in server:\n";
print_r($missingInServer);

echo "\nMissing in client:\n";
print_r($missingInClient);
