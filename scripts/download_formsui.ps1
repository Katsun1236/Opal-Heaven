$headers = @{ 'User-Agent' = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)' }
$formsData = Invoke-RestMethod -Uri "https://poggit.pmmp.io/releases.json?name=FormsUI" -Headers $headers
if ($formsData -and $formsData.Count -gt 0) {
    $latest = $formsData[0]
    Write-Host "Found FormsUI: $($latest.artifact_url)"
    Invoke-WebRequest -Uri $latest.artifact_url -OutFile "c:\Users\basti\OneDrive\Bureau\Opal Heaven\server\plugins\FormsUI.phar" -Headers $headers
    $item = Get-Item "c:\Users\basti\OneDrive\Bureau\Opal Heaven\server\plugins\FormsUI.phar"
    Write-Host "FormsUI size: $($item.Length) bytes"
}
