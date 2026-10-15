$path = 'C:\XboxGames\Minecraft for Windows\Content\Minecraft.Windows.exe'
$fs = [System.IO.File]::Open($path, [System.IO.FileMode]::Open, [System.IO.FileAccess]::Read, [System.IO.FileShare]::ReadWrite)
Write-Host "Opened successfully! Length: $($fs.Length)"
$fs.Close()
