<?php
$json = file_get_contents('https://api.github.com/repos/sandertv/gophertunnel/commits?path=minecraft/protocol/packet/level_chunk.go', false, stream_context_create([
    'http' => ['header' => 'User-Agent: PHP']
]));
$commits = json_decode($json, true);
$sha = $commits[0]['sha'];
$commitJson = file_get_contents("https://api.github.com/repos/sandertv/gophertunnel/commits/$sha", false, stream_context_create([
    'http' => ['header' => 'User-Agent: PHP']
]));
$commitData = json_decode($commitJson, true);
foreach ($commitData['files'] as $f) {
    if (strpos($f['filename'], 'level_chunk') !== false) {
        echo "FILE: " . $f['filename'] . "\n";
        echo $f['patch'] . "\n";
    }
}
