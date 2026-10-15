<?php
$dirs = [
    getenv('APPDATA') . '/Minecraft Bedrock',
    getenv('LOCALAPPDATA') . '/Packages/Microsoft.MinecraftUWP_8wekyb3d8bbwe'
];

$all = [];
foreach ($dirs as $base) {
    if (!is_dir($base)) continue;
    try {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile()) {
                $all[] = ['time' => $file->getMTime(), 'size' => $file->getSize(), 'path' => $file->getPathname()];
            }
        }
    } catch (\Throwable $e) {}
}
usort($all, fn($a, $b) => $b['time'] <=> $a['time']);
echo "Current time: " . date("Y-m-d H:i:s") . "\n";
foreach (array_slice($all, 0, 20) as $f) {
    echo date("Y-m-d H:i:s", $f['time']) . " [" . $f['size'] . "b] " . $f['path'] . "\n";
}

