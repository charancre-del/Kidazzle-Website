$urls = @(
    "https://stockbridgedaycare.com/",
    "https://stockbridgedaycare.com/programs/",
    "https://stockbridgedaycare.com/program/infants/",
    "https://stockbridgedaycare.com/contact/",
    "https://stockbridgedaycare.com/about-us/",
    "https://stockbridgedaycare.com/locations/"
)

$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"

foreach ($url in $urls) {
    Write-Host "Warming cache for: $url"
    try {
        $response = Invoke-WebRequest -Uri $url -UserAgent $userAgent -UseBasicParsing
        Write-Host "Status: $($response.StatusCode)"
    } catch {
        Write-Host "Failed to fetch $url : $_"
    }
}
