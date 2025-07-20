# PowerShell script to run CodeIgniter 3 project with local PHP
$phpPath = Join-Path $PSScriptRoot 'php\php.exe'
$phpDir = Join-Path $PSScriptRoot 'php'
$extDir = Join-Path $phpDir 'ext'
$webRoot = $PSScriptRoot
$port = 8080

# Set PHPRC to use local php.ini
$env:PHPRC = $phpDir

# Cool ASCII Banner
Write-Host "" -ForegroundColor Cyan
Write-Host "██████╗  █████╗ ███████╗     ███████╗██████╗ ██████╗ " -ForegroundColor DarkBlue
Write-Host "██╔══██╗██╔══██╗██╔════╝     ██╔════╝██╔══██╗██╔══██╗" -ForegroundColor DarkBlue
Write-Host "██████╔╝███████║█████╗       █████╗  ██████╔╝██████╔╝" -ForegroundColor Blue
Write-Host "██╔═══╝ ██╔══██║██╔══╝       ██╔══╝  ██╔══██╗██╔═══╝ " -ForegroundColor Blue
Write-Host "██║     ██║  ██║███████╗     ███████╗██║  ██║██║     " -ForegroundColor Yellow
Write-Host "╚═╝     ╚═╝  ╚═╝╚══════╝     ╚══════╝╚═╝  ╚═╝╚═╝     " -ForegroundColor Yellow
Write-Host "" -ForegroundColor Cyan
Write-Host "        PAE ERP - CodeIgniter 3 Local Server" -ForegroundColor White
Write-Host "        Server by: LJ Faderon" -ForegroundColor DarkGray
Write-Host "────────────────────────────────────────────────────────────" -ForegroundColor DarkGray
Write-Host ("  Project Root: " + $webRoot) -ForegroundColor Cyan
Write-Host ("  PHP Version:  " + (& $phpPath -d extension_dir=$extDir -v | Select-Object -First 1)) -ForegroundColor Cyan
Write-Host ("  Server URL:   http://localhost:$port") -ForegroundColor Yellow
Write-Host "────────────────────────────────────────────────────────────" -ForegroundColor DarkGray
Write-Host "  Press Ctrl+C to stop the server." -ForegroundColor DarkGray
Write-Host "" -ForegroundColor Cyan

# Check for mysqli extension with correct extension_dir
$mysqliLoaded = & $phpPath -d extension_dir=$extDir -m | Select-String -Pattern 'mysqli'
if (-not $mysqliLoaded) {
    Write-Host "[ERROR] The 'mysqli' extension is not enabled or not found in $extDir. Please check your php.ini and extension_dir settings." -ForegroundColor Red
    exit 1
}

Write-Host "Starting PHP built-in server at http://localhost:$port" -ForegroundColor Green
& $phpPath -d extension_dir=$extDir -S localhost:$port -t $webRoot