# Git Sync PowerShell Script with Colors
Write-Host ""
Write-Host "╔═══════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║          🚀 GIT SYNC TOOL 🚀          ║" -ForegroundColor Green
Write-Host "╚═══════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

# Function to run git commands with error handling
function Run-GitCommand {
    param($Command, $Description, $Color)
    
    Write-Host "$Description" -ForegroundColor $Color
    $result = Invoke-Expression $Command
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Success!" -ForegroundColor Green
    } else {
        Write-Host "❌ Error occurred!" -ForegroundColor Red
        Write-Host "$result" -ForegroundColor Red
    }
    Write-Host ""
}

# Execute git commands
Run-GitCommand "git add ." "📁 Adding all changes..." "Yellow"
Run-GitCommand "git commit -m 'updated'" "💾 Committing changes..." "Cyan"
Run-GitCommand "git pull origin main" "⬇️  Pulling latest changes from main..." "Blue"
Run-GitCommand "git push origin main" "⬆️  Pushing changes to main..." "Magenta"

Write-Host "🎉 Git sync completed! 🎉" -ForegroundColor Green
Write-Host ""
