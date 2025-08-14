<<<<<<< HEAD
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
=======
# PAE ERP - CodeIgniter 3 Local Server (PowerShell)
# Server by: LJ Faderon

$phpPath = Join-Path $PSScriptRoot 'php\php.exe'
$phpIni = Join-Path $PSScriptRoot 'php\php.ini'
$webRoot = $PSScriptRoot
$port = 8080

Write-Host ""
Write-Host "██████╗  █████╗ ███████╗     ███████╗██████╗ ██████╗ " -ForegroundColor Blue
Write-Host "██╔══██╗██╔══██╗██╔════╝     ██╔════╝██╔══██╗██╔══██╗" -ForegroundColor Blue
>>>>>>> phase1
Write-Host "██████╔╝███████║█████╗       █████╗  ██████╔╝██████╔╝" -ForegroundColor Blue
Write-Host "██╔═══╝ ██╔══██║██╔══╝       ██╔══╝  ██╔══██╗██╔═══╝ " -ForegroundColor Blue
Write-Host "██║     ██║  ██║███████╗     ███████╗██║  ██║██║     " -ForegroundColor Yellow
Write-Host "╚═╝     ╚═╝  ╚═╝╚══════╝     ╚══════╝╚═╝  ╚═╝╚═╝     " -ForegroundColor Yellow
<<<<<<< HEAD
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
=======
Write-Host ""
Write-Host "        PAE ERP - CodeIgniter 3 Local Server" -ForegroundColor White
Write-Host "        Server by: LJ Faderon" -ForegroundColor DarkGray
Write-Host "────────────────────────────────────────────────────────────" -ForegroundColor DarkGray
Write-Host "  Project Root: $webRoot" -ForegroundColor Cyan
Write-Host "  PHP Version:  $(& $phpPath -c $phpIni -v | Select-Object -First 1)" -ForegroundColor Cyan
Write-Host "  Server URL:   http://localhost:$port" -ForegroundColor Yellow
Write-Host "────────────────────────────────────────────────────────────" -ForegroundColor DarkGray
Write-Host "  Press Ctrl+C to stop the server." -ForegroundColor DarkGray
Write-Host ""

# Check for mysqli extension
$phpModules = & $phpPath -c $phpIni -m
if ($phpModules -notmatch 'mysqli') {
    Write-Host "[ERROR] The 'mysqli' extension is not enabled or not found. Please check your php.ini in $phpIni and extension_dir settings." -ForegroundColor Red
>>>>>>> phase1
    exit 1
}

Write-Host "Starting PHP built-in server at http://localhost:$port" -ForegroundColor Green
<<<<<<< HEAD
& $phpPath -d extension_dir=$extDir -S localhost:$port -t $webRoot
=======
& $phpPath -c $phpIni -S localhost:$port -t $webRoot
>>>>>>> phase1
