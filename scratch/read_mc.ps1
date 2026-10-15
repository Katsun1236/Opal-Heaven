try {
    $fs = [System.IO.File]::Open('C:\XboxGames\Minecraft for Windows\Content\Minecraft.Windows.exe', [System.IO.FileMode]::Open, [System.IO.FileAccess]::Read, [System.IO.FileShare]::ReadWrite)
    Write-Host "Success opening Minecraft.Windows.exe! Length: $($fs.Length)"
    $fs.Close()
} catch {
    Write-Host "Error: $_"
}
