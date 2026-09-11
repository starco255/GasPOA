param(
    [string]$CloudflaredPath = 'cloudflared.exe',
    [int]$Port = 8000
)

if (-not (Get-Command $CloudflaredPath -ErrorAction SilentlyContinue) -and -not (Test-Path -LiteralPath $CloudflaredPath)) {
    throw "cloudflared haijapatikana. Weka cloudflared.exe kwenye PATH au pitisha njia kamili: -CloudflaredPath 'C:\\njia\\cloudflared.exe'."
}

# A quick tunnel creates a fresh trycloudflare.com URL every time it starts.
# Keep this terminal open while the URL should remain reachable.
& $CloudflaredPath tunnel --url "http://127.0.0.1:$Port"
