<?php
function searchDir($dir, $pattern) {
    if(!is_dir($dir)) return;
    foreach(scandir($dir) as $f) {
        if($f === '.' || $f === '..') continue;
        $path = $dir . '/' . $f;
        if(is_dir($path)) {
            searchDir($path, $pattern);
        } elseif(str_ends_with($f, '.php')) {
            $content = file_get_contents($path);
            if(stripos($content, $pattern) !== false) {
                echo "$path\n";
            }
        }
    }
}
$dir = $argv[2] ?? 'server/pmmp_src/src';
$pattern = $argv[1] ?? 'blockPalette';
searchDir($dir, $pattern);
