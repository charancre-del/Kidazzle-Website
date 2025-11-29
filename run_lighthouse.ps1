$urls = @(
    "https://stockbridgedaycare.com/",
    "https://stockbridgedaycare.com/programs/",
    "https://stockbridgedaycare.com/contact/",
    "https://stockbridgedaycare.com/about/",
    "https://stockbridgedaycare.com/locations/",
    "https://stockbridgedaycare.com/program/infants/",
    "https://stockbridgedaycare.com/program/toddlers/"
)

foreach ($url in $urls) {
    $name = $url -replace "https://stockbridgedaycare.com/", "" -replace "/", "_"
    if ($name -eq "") { $name = "home" }
    $name = $name.Trim("_")
    
    Write-Host "Running Lighthouse for $url..."
    $cmd = "npx lighthouse $url --output json --output-path report_$name.json --chrome-flags='--headless' --only-categories=performance"
    Invoke-Expression $cmd
}
