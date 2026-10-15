$ErrorActionPreference = "Stop"

$workspace = "c:\Users\basti\OneDrive\Bureau\Opal Heaven"
$serverDir = Join-Path $workspace "server"
$binDir = Join-Path $serverDir "bin"
$pluginsDir = Join-Path $serverDir "plugins"
$resourcePacksDir = Join-Path $serverDir "resource_packs"
$networkDir = Join-Path $workspace "network"
$databaseDir = Join-Path $workspace "database"

Write-Host "Creating directories..." -ForegroundColor Cyan
New-Item -ItemType Directory -Force -Path $serverDir | Out-Null
New-Item -ItemType Directory -Force -Path $binDir | Out-Null
New-Item -ItemType Directory -Force -Path $pluginsDir | Out-Null
New-Item -ItemType Directory -Force -Path $resourcePacksDir | Out-Null
New-Item -ItemType Directory -Force -Path $networkDir | Out-Null
New-Item -ItemType Directory -Force -Path $databaseDir | Out-Null

Write-Host "Fetching latest PocketMine-MP and PHP Binaries releases info..." -ForegroundColor Cyan
$headers = @{ 'User-Agent' = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)' }

$phpReleases = Invoke-RestMethod -Uri "https://api.github.com/repos/pmmp/PHP-Binaries/releases" -Headers $headers
$targetPhpAsset = $null

foreach ($rel in $phpReleases) {
    foreach ($asset in $rel.assets) {
        if ($asset.name -like "*Windows*x64*PM5*.zip") {
            $targetPhpAsset = $asset
            break
        }
    }
    if ($targetPhpAsset -ne $null) { break }
}

if ($targetPhpAsset -eq $null) {
    # Fallback to any windows x64 zip
    foreach ($rel in $phpReleases) {
        foreach ($asset in $rel.assets) {
            if ($asset.name -like "*Windows*x64*.zip") {
                $targetPhpAsset = $asset
                break
            }
        }
        if ($targetPhpAsset -ne $null) { break }
    }
}

Write-Host "Found PHP Asset: $($targetPhpAsset.name)" -ForegroundColor Green
Write-Host "URL: $($targetPhpAsset.browser_download_url)" -ForegroundColor Yellow

$pmRelease = Invoke-RestMethod -Uri "https://api.github.com/repos/pmmp/PocketMine-MP/releases/latest" -Headers $headers
$targetPmAsset = $null
foreach ($asset in $pmRelease.assets) {
    if ($asset.name -eq "PocketMine-MP.phar") {
        $targetPmAsset = $asset
        break
    }
}

Write-Host "Found PocketMine Asset: $($targetPmAsset.name) ($($pmRelease.tag_name))" -ForegroundColor Green
Write-Host "URL: $($targetPmAsset.browser_download_url)" -ForegroundColor Yellow

# Download PHP binary zip
$phpZipPath = Join-Path $serverDir "php.zip"
Write-Host "Downloading PHP Binary package..." -ForegroundColor Cyan
Invoke-WebRequest -Uri $targetPhpAsset.browser_download_url -OutFile $phpZipPath -Headers $headers

Write-Host "Extracting PHP Binary..." -ForegroundColor Cyan
Expand-Archive -Path $phpZipPath -DestinationPath $binDir -Force
Remove-Item $phpZipPath -Force

# Download PocketMine-MP.phar
$pmPharPath = Join-Path $serverDir "PocketMine-MP.phar"
Write-Host "Downloading PocketMine-MP.phar..." -ForegroundColor Cyan
Invoke-WebRequest -Uri $targetPmAsset.browser_download_url -OutFile $pmPharPath -Headers $headers

Write-Host "PocketMine-MP installation files successfully set up!" -ForegroundColor Green
