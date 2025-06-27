# Git Sync PowerShell Script with Colorful CLI
# Usage: .\git-sync.ps1 "Your commit message"
# Example: .\git-sync.ps1 "Added new API endpoint"

param(
    [Parameter(Mandatory=$false)]
    [string]$Message
)

# Color and icon functions
function Write-ColorMessage {
    param(
        [string]$Message,
        [string]$Color = "White",
        [string]$Icon = "[INFO]"
    )
    Write-Host "$Icon " -NoNewline
    Write-Host $Message -ForegroundColor $Color
}

function Write-Success {
    param([string]$Message)
    Write-ColorMessage $Message "Green" "[✓]"
}

function Write-Info {
    param([string]$Message)
    Write-ColorMessage $Message "Cyan" "[i]"
}

function Write-Warning {
    param([string]$Message)
    Write-ColorMessage $Message "Yellow" "[!]"
}

function Write-Error {
    param([string]$Message)
    Write-ColorMessage $Message "Red" "[✗]"
}

function Write-Progress {
    param([string]$Message)
    Write-ColorMessage $Message "Magenta" "[→]"
}

function Write-Header {
    param([string]$Message)
    Write-Host ""
    Write-Host "=" * 60 -ForegroundColor DarkGray
    Write-ColorMessage $Message "White" "[*]"
    Write-Host "=" * 60 -ForegroundColor DarkGray
    Write-Host ""
}

# Interactive prompt if no message provided
if (-not $Message) {
    [Console]::OutputEncoding = [System.Text.Encoding]::UTF8
    Write-Host ""
    Write-Host "🚀 Git Sync - By: LJ Faderon" -ForegroundColor Cyan
    Write-Host "───────────────────────────────────────" -ForegroundColor DarkGray
    Write-Host "⚡ Auto-stage • Smart commit • Push changes" -ForegroundColor Yellow
    Write-Host "───────────────────────────────────────" -ForegroundColor DarkGray
    Write-Host ""
    Write-Host "Please enter your commit message" -ForegroundColor White
    $Message = Read-Host "Message"
    
    if (-not $Message) {
        Write-Host "Operation cancelled." -ForegroundColor Red
        exit 0
    }
}

# Set console to UTF-8 for emoji support
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# Main script execution
try {
    Write-Header "Git Sync Script - LJFaderon.COM API"
    
    # Check if we're in a git repository
    Write-Progress "Checking git repository status..."
    if (-not (Test-Path ".git")) {
        Write-Error "Not a git repository! Please run 'git init' first."
        exit 1
    }
    Write-Success "Git repository detected"
    
    # Check git status
    Write-Progress "Checking for changes..."
    $gitStatus = git status --porcelain
    if (-not $gitStatus) {
        Write-Warning "No changes detected. Nothing to commit."
        exit 0
    }
    
    # Show what files will be added
    Write-Info "Files to be committed:"
    git status --short | ForEach-Object {
        Write-Host "  FILE: $_" -ForegroundColor Gray
    }
    Write-Host ""
    
    # Add all changes
    Write-Progress "Adding all changes to staging area..."
    git add .
    if ($LASTEXITCODE -eq 0) {
        Write-Success "All changes staged successfully"
    } else {
        Write-Error "Failed to stage changes"
        exit 1
    }
    
    # Commit changes
    Write-Progress "Committing changes with message: '$Message'"
    git commit -m $Message
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Changes committed successfully"
    } else {
        Write-Error "Failed to commit changes"
        exit 1
    }
    
    # Check if remote origin exists
    Write-Progress "Checking remote repository..."
    $remoteUrl = git remote get-url origin 2>$null
    if (-not $remoteUrl) {
        Write-Warning "No remote origin found. Skipping push."
        Write-Info "To add a remote origin, run:"
        Write-Host "  git remote add origin <repository-url>" -ForegroundColor Gray
        exit 0
    }
    Write-Success "Remote origin found: $remoteUrl"
    
    # Get current branch
    $currentBranch = git branch --show-current
    Write-Info "Current branch: $currentBranch"
    
    # Pull latest changes first
    Write-Progress "Pulling latest changes from remote..."
    git pull origin $currentBranch --rebase
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Successfully pulled latest changes"
    } else {
        Write-Warning "Pull completed with conflicts or warnings (this is often normal)"
    }
    
    # Push changes
    Write-Progress "Pushing changes to remote repository..."
    git push origin $currentBranch
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Changes pushed successfully to $currentBranch"
    } else {
        Write-Error "Failed to push changes"
        Write-Info "You may need to resolve conflicts or check your credentials"
        exit 1
    }
    
    # Show final status
    Write-Header "Git Sync Complete!"
    Write-Success "Repository is now synchronized"
    Write-Info "Commit message: '$Message'"
    Write-Info "Branch: $currentBranch"
    Write-Info "Remote: $remoteUrl"
    
    # Show last few commits
    Write-Host ""
    Write-ColorMessage "Recent commits:" "White" "[COMMITS]"
    git log --oneline -5 | ForEach-Object {
        Write-Host "  COMMIT: $_" -ForegroundColor DarkGray
    }
    
} catch {
    Write-Error "An unexpected error occurred: $($_.Exception.Message)"
    exit 1
}

Write-Host ""
Write-ColorMessage "Git sync completed successfully!" "Green" "[COMPLETE]"
