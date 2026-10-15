<?php
$b30Path = 'C:/XboxGames/Minecraft for Windows/Content/data/resource_packs/vanilla_1.26.30/blocks.json';
$b50Path = 'C:/XboxGames/Minecraft for Windows/Content/data/resource_packs/vanilla_1.26.50/blocks.json';

if (file_exists($b30Path) && file_exists($b50Path)) {
    $b30 = json_decode(file_get_contents($b30Path), true);
    $b50 = json_decode(file_get_contents($b50Path), true);
    
    $diffNew = array_diff_key($b50, $b30);
    $diffRemoved = array_diff_key($b30, $b50);
    
    echo "New blocks in 1.26.50: " . count($diffNew) . "\n";
    print_r(array_keys($diffNew));
    echo "Removed blocks in 1.26.50: " . count($diffRemoved) . "\n";
    print_r(array_keys($diffRemoved));
} else {
    echo "Files not found.\n";
}
