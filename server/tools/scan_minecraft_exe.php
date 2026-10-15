<?php
$exePath = "C:/XboxGames/Minecraft for Windows/Content/Minecraft.Windows.exe";
if (!file_exists($exePath)) {
    echo "Fichier introuvable: $exePath\n";
    exit(1);
}

echo "Scan des chaînes dans $exePath...\n";
$handle = fopen($exePath, "rb");
$bufferSize = 8 * 1024 * 1024; // 8 Mo
$offset = 0;
$matches = [];

$searchTerms = [
    "InitialConnection-148",
    "Blocks from the client and server do not match",
    "InitialConnection",
    "blockNetworkIdsAreHashes"
];

$tail = "";
while (!feof($handle)) {
    $chunk = $tail . fread($handle, $bufferSize);
    foreach ($searchTerms as $term) {
        $pos = 0;
        while (($pos = strpos($chunk, $term, $pos)) !== false) {
            $start = max(0, $pos - 100);
            $context = substr($chunk, $start, 250);
            $printable = preg_replace('/[^\x20-\x7E]/', '.', $context);
            $matches[] = "Terme '$term' trouvé à l'offset global: " . ($offset + $pos) . "\n  Contexte: $printable\n";
            $pos += strlen($term);
        }
    }
    $tail = substr($chunk, -500);
    $offset += ($bufferSize - 500);
}
fclose($handle);

echo "Total correspondances trouvées: " . count($matches) . "\n";
foreach (array_slice($matches, 0, 10) as $m) {
    echo $m . "\n";
}
