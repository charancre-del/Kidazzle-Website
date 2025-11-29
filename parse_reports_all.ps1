$files = Get-ChildItem "report_*.json"
$mobileReports = @()
$desktopReports = @()

foreach ($file in $files) {
    $json = Get-Content $file.FullName | ConvertFrom-Json
    $score = $json.categories.performance.score * 100
    $lcp = $json.audits.'largest-contentful-paint'.displayValue
    $fcp = $json.audits.'first-contentful-paint'.displayValue
    $tbt = $json.audits.'total-blocking-time'.displayValue
    $cls = $json.audits.'cumulative-layout-shift'.displayValue
    
    $name = $file.Name -replace "report_", "" -replace ".json", "" -replace "mobile_", "" -replace "desktop_", "" -replace "_", " "
    $name = (Get-Culture).TextInfo.ToTitleCase($name)
    
    $report = [PSCustomObject]@{
        Page = $name
        Score = $score
        LCP = $lcp
        FCP = $fcp
        TBT = $tbt
        CLS = $cls
    }

    if ($file.Name -like "*mobile*") {
        $mobileReports += $report
    } elseif ($file.Name -like "*desktop*") {
        $desktopReports += $report
    }
}

Write-Host "### Mobile Performance"
Write-Host "| Page | Score | LCP | FCP | TBT | CLS |"
Write-Host "|---|---|---|---|---|---|"
foreach ($r in $mobileReports) {
    Write-Host "| $($r.Page) | $($r.Score) | $($r.LCP) | $($r.FCP) | $($r.TBT) | $($r.CLS) |"
}

Write-Host ""
Write-Host "### Desktop Performance"
Write-Host "| Page | Score | LCP | FCP | TBT | CLS |"
Write-Host "|---|---|---|---|---|---|"
foreach ($r in $desktopReports) {
    Write-Host "| $($r.Page) | $($r.Score) | $($r.LCP) | $($r.FCP) | $($r.TBT) | $($r.CLS) |"
}
