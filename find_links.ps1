param([string]$url, [string]$pattern)

$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"

try {
    $response = Invoke-WebRequest -Uri $url -UserAgent $userAgent -UseBasicParsing
    $content = $response.Content
    
    # Simple regex to find hrefs
    $matches = [regex]::Matches($content, 'href="([^"]*)"')
    foreach ($match in $matches) {
        $link = $match.Groups[1].Value
        if ($link -match $pattern) {
            Write-Host $link
        }
    }
} catch {
    Write-Host "Error: $_"
}
