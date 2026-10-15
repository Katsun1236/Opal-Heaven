$path = 'C:\Program Files\WindowsApps\Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe\Minecraft.Windows.exe'
try {
    $s = [System.IO.File]::OpenRead($path)
    Write-Host "Success, length: $($s.Length)"
    $s.Close()
} catch {
    Write-Host "Failed: $_"
}
