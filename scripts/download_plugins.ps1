$headers = @{ 'User-Agent' = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)' }

Write-Host "Fetching Customies..." -ForegroundColor Cyan
$customiesData = Invoke-RestMethod -Uri "https://poggit.pmmp.io/releases.json?name=Customies" -Headers $headers
$latest = $customiesData[0]
$downloadUrl = $latest.artifact_url
if (-not ($downloadUrl.StartsWith("http"))) {
    $downloadUrl = "https://poggit.pmmp.io/r/" + $downloadUrl + "/Customies.phar"
}
Write-Host "Downloading Customies from: $downloadUrl" -ForegroundColor Yellow
Invoke-WebRequest -Uri $downloadUrl -OutFile "c:\Users\basti\OneDrive\Bureau\Opal Heaven\server\plugins\Customies.phar" -Headers $headers
$cFile = Get-Item "c:\Users\basti\OneDrive\Bureau\Opal Heaven\server\plugins\Customies.phar"
Write-Host "Customies size: $($cFile.Length) bytes" -ForegroundColor Green

# Query FormAPI
Write-Host "Fetching FormAPI..." -ForegroundColor Cyan
$formData = Invoke-RestMethod -Uri "https://poggit.pmmp.io/releases.json?name=FormAPI" -Headers $headers
if ($formData -and $formData.Count -gt 0) {
    $latestForm = $formData[0]
    $formDownloadUrl = $latestForm.artifact_url
    if (-not ($formDownloadUrl.StartsWith("http"))) {
        $formDownloadUrl = "https://poggit.pmmp.io/r/" + $formDownloadUrl + "/FormAPI.phar"
    }
    Write-Host "Downloading FormAPI from: $formDownloadUrl" -ForegroundColor Yellow
    Invoke-WebRequest -Uri $formDownloadUrl -OutFile "c:\Users\basti\OneDrive\Bureau\Opal Heaven\server\plugins\FormAPI.phar" -Headers $headers
    $fFile = Get-Item "c:\Users\basti\OneDrive\Bureau\Opal Heaven\server\plugins\FormAPI.phar"
    Write-Host "FormAPI size: $($fFile.Length) bytes" -ForegroundColor Green
}
