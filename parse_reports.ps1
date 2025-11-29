$files = Get-ChildItem "report_*.json"

Write-Host "| Page | Score | LCP | FCP | TBT | CLS |"
Write-Host "|---|---|---|---|---|---|"

foreach ($file in $files) {
    $json = Get-Content $file.FullName | ConvertFrom-Json
    $score = $json.categories.performance.score * 100
    $lcp = $json.audits.'largest-contentful-paint'.displayValue
    $fcp = $json.audits.'first-contentful-paint'.displayValue
    $tbt = $json.audits.'total-blocking-time'.displayValue
    $cls = $json.audits.'cumulative-layout-shift'.displayValue
    
    $name = $file.Name -replace "report_", "" -replace ".json", "" -replace "_", " "
    $name = (Get-Culture).TextInfo.ToTitleCase($name)
    
    Write-Host "| $name | $score | $lcp | $fcp | $tbt | $cls |"
}
