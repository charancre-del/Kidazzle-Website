$urls = @(
    "https://stockbridgedaycare.com/",
    "https://stockbridgedaycare.com/programs/",
    "https://stockbridgedaycare.com/contact/",
    "https://stockbridgedaycare.com/about/",
    "https://stockbridgedaycare.com/locations/"
)

foreach ($url in $urls) {
    $name = $url -replace "https://stockbridgedaycare.com/", "" -replace "/", "_"
    if ($name -eq "") { $name = "home" }
    $name = $name.Trim("_")
    
    # Mobile Audit
    Write-Host "Running Mobile Lighthouse for $url..."
    $cmdMobile = "npx lighthouse $url --output json --output-path report_mobile_$name.json --chrome-flags='--headless' --only-categories=performance --emulated-form-factor=mobile"
    Invoke-Expression $cmdMobile

    # Desktop Audit
    Write-Host "Running Desktop Lighthouse for $url..."
    $cmdDesktop = "npx lighthouse $url --output json --output-path report_desktop_$name.json --chrome-flags='--headless' --only-categories=performance --preset=desktop"
    Invoke-Expression $cmdDesktop
}
