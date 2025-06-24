@echo off
color 0A
echo.
echo [92m╔═══════════════════════════════════════╗[0m
echo [92m║          🚀 GIT SYNC TOOL 🚀          ║[0m
echo [92m╚═══════════════════════════════════════╝[0m
echo.

echo [93m📁 Adding all changes...[0m
git add .
if %errorlevel% equ 0 (
    echo [92m✅ Files added successfully![0m
) else (
    echo [91m❌ Error adding files![0m
)
echo.

echo [96m💾 Committing changes...[0m
git commit -m "updated"
if %errorlevel% equ 0 (
    echo [92m✅ Changes committed successfully![0m
) else (
    echo [93m⚠️  No changes to commit or commit failed![0m
)
echo.

echo [94m⬇️  Pulling latest changes from main...[0m
git pull origin main
if %errorlevel% equ 0 (
    echo [92m✅ Pull completed successfully![0m
) else (
    echo [91m❌ Error during pull![0m
)
echo.

echo [95m⬆️  Pushing changes to main...[0m
git push origin main
if %errorlevel% equ 0 (
    echo [92m✅ Push completed successfully![0m
) else (
    echo [91m❌ Error during push![0m
)
echo.

echo [92m🎉 Git sync completed! 🎉[0m
echo.
echo [97mPress any key to continue...[0m
pause > nul
