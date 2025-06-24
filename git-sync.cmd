@echo off
echo Starting Git sync process...
echo.

echo Adding all changes...
git add .

echo.
echo Committing changes...
git commit -m "updated"

echo.
echo Pulling latest changes from main...
git pull origin main

echo.
echo Pushing changes to main...
git push origin main

echo.
echo Git sync completed!
echo.
echo Press any key to continue...
pause > nul
