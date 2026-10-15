$headers = @{ 'User-Agent' = 'Mozilla/5.0' }
$releases = Invoke-RestMethod -Uri "https://api.github.com/repos/pmmp/PocketMine-MP/releases?per_page=10" -Headers $headers
foreach ($rel in $releases) {
    Write-Host "Tag: $($rel.tag_name) | Name: $($rel.name) | Prerelease: $($rel.prerelease)"
    foreach ($a in $rel.assets) {
        if ($a.name -like "*.phar") {
            Write-Host "  Asset: $($a.name) -> $($a.browser_download_url)"
        }
    }
}
