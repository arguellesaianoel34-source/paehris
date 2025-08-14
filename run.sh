#!/bin/bash


# Ensure php@7.4 is first in PATH
export PATH="/opt/homebrew/opt/php@7.4/bin:/opt/homebrew/opt/php@7.4/sbin:$PATH"

phpPath="/opt/homebrew/opt/php@7.4/bin/php"
phpDir="/opt/homebrew/etc/php/7.4"
extDir="/opt/homebrew/opt/php@7.4/lib/php/20190902"
webRoot="$(pwd)"
port=8080

export PHPRC="$phpDir"

# Cool ASCII Banner
echo ""
echo -e "\033[34m██████╗  █████╗ ███████╗     ███████╗██████╗ ██████╗ \033[0m"
echo -e "\033[34m██╔══██╗██╔══██╗██╔════╝     ██╔════╝██╔══██╗██╔══██╗\033[0m"
echo -e "\033[34m██████╔╝███████║█████╗       █████╗  ██████╔╝██████╔╝\033[0m"
echo -e "\033[34m██╔═══╝ ██╔══██║██╔══╝       ██╔══╝  ██╔══██╗██╔═══╝ \033[0m"
echo -e "\033[33m██║     ██║  ██║███████╗     ███████╗██║  ██║██║     \033[0m"
echo -e "\033[33m╚═╝     ╚═╝  ╚═╝╚══════╝     ╚══════╝╚═╝  ╚═╝╚═╝     \033[0m"
echo ""
echo -e "\033[37m        PAE ERP - CodeIgniter 3 Local Server\033[0m"
echo -e "\033[90m        Server by: LJ Faderon\033[0m"
echo -e "\033[90m────────────────────────────────────────────────────────────\033[0m"
echo -e "\033[36m  Project Root: $webRoot\033[0m"
phpVersionOutput=$($phpPath -c $phpDir/php.ini -v | head -n 1)
echo -e "\033[36m  PHP Version:  $phpVersionOutput\033[0m"
echo -e "\033[33m  Server URL:   http://localhost:$port\033[0m"
echo -e "\033[90m────────────────────────────────────────────────────────────\033[0m"
echo -e "\033[90m  Press Ctrl+C to stop the server.\033[0m"
echo ""


# Check PHP version is 7.4
if ! echo "$phpVersionOutput" | grep -q "PHP 7.4"; then
    echo -e "\033[31m[ERROR] PHP 7.4 is required. Current version: $phpVersionOutput\033[0m"
    exit 1
fi

# Check mysqli extension
if ! $phpPath -c $phpDir/php.ini -m | grep -q 'mysqli'; then
    echo -e "\033[31m[ERROR] The 'mysqli' extension is not enabled or not found. Please check your php.ini in $phpDir and extension_dir settings.\033[0m"
    exit 1
fi

echo -e "\033[32mStarting PHP built-in server at http://localhost:$port\033[0m"
$phpPath -c $phpDir/php.ini -S localhost:$port -t "$webRoot"
