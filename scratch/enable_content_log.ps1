$targetPath = "C:\Users\basti\AppData\Roaming\Minecraft Bedrock\Users\4542480576725733784\games\com.mojang\minecraftpe\options.txt"
if (Test-Path $targetPath) {
    $content = Get-Content $targetPath -Raw
    $content = $content -replace 'content_log_file:0', 'content_log_file:1'
    $content = $content -replace 'content_log_gui:0', 'content_log_gui:1'
    Set-Content -Path $targetPath -Value $content -NoNewline
    Write-Output "Successfully modified options.txt"
} else {
    Write-Output "Target not found: $targetPath"
}
