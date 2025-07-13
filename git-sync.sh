#!/bin/bash
# Git Sync Bash Script with Colorful CLI
# Usage: ./git-sync.sh "Your commit message"
# Example: ./git-sync.sh "Added new API endpoint"

# Color and icon functions
function write_color_message() {
    local message="$1"
    local color="$2"
    local icon="$3"
    local color_code=""
    case $color in
        "Green") color_code="\033[0;32m" ;;
        "Cyan") color_code="\033[0;36m" ;;
        "Yellow") color_code="\033[1;33m" ;;
        "Red") color_code="\033[0;31m" ;;
        "Magenta") color_code="\033[0;35m" ;;
        "White"|*) color_code="\033[1;37m" ;;
    esac
    echo -e -n "$icon "
    echo -e "${color_code}${message}\033[0m"
}

function write_success() { write_color_message "$1" "Green" "[✓]"; }
function write_info()    { write_color_message "$1" "Cyan" "[i]"; }
function write_warning() { write_color_message "$1" "Yellow" "[!]"; }
function write_error()   { write_color_message "$1" "Red" "[✗]"; }
function write_progress(){ write_color_message "$1" "Magenta" "[→]"; }
function write_header() {
    local message="$1"
    echo
    echo -e "\033[1;30m============================================================\033[0m"
    write_color_message "$message" "White" "[*]"
    echo -e "\033[1;30m============================================================\033[0m"
    echo
}

# Interactive prompt if no message provided
if [ -z "$1" ]; then
    echo
    echo -e "\033[0;36m🚀 Git Sync - By: LJ Faderon\033[0m"
    echo -e "\033[1;30m───────────────────────────────────────\033[0m"
    echo -e "\033[1;33m⚡ Auto-stage • Smart commit • Push changes\033[0m"
    echo -e "\033[1;30m───────────────────────────────────────\033[0m"
    echo
    echo -e "\033[1;37mPlease enter your commit message\033[0m"
    read -r -p "Message: " MESSAGE
    if [ -z "$MESSAGE" ]; then
        echo -e "\033[0;31mOperation cancelled.\033[0m"
        exit 0
    fi
else
    MESSAGE="$1"
fi

# Main script execution
write_header "Git Sync Script - LJFaderon.COM API"

# Check if we're in a git repository
write_progress "Checking git repository status..."
if [ ! -d .git ]; then
    write_error "Not a git repository! Please run 'git init' first."
    exit 1
fi
write_success "Git repository detected"

# Check git status
write_progress "Checking for changes..."
GIT_STATUS=$(git status --porcelain)
if [ -z "$GIT_STATUS" ]; then
    write_warning "No changes detected. Nothing to commit."
    exit 0
fi

# Show what files will be added
write_info "Files to be committed:"
git status --short | while read -r line; do
    echo -e "  FILE: \033[1;30m$line\033[0m"
done

echo

# Add all changes
write_progress "Adding all changes to staging area..."
git add .
if [ $? -eq 0 ]; then
    write_success "All changes staged successfully"
else
    write_error "Failed to stage changes"
    exit 1
fi

# Commit changes
write_progress "Committing changes with message: '$MESSAGE'"
git commit -m "$MESSAGE"
if [ $? -eq 0 ]; then
    write_success "Changes committed successfully"
else
    write_error "Failed to commit changes"
    exit 1
fi

# Check if remote origin exists
write_progress "Checking remote repository..."
REMOTE_URL=$(git remote get-url origin 2>/dev/null)
if [ -z "$REMOTE_URL" ]; then
    write_warning "No remote origin found. Skipping push."
    write_info "To add a remote origin, run:"
    echo -e "  \033[1;30mgit remote add origin <repository-url>\033[0m"
    exit 0
fi
write_success "Remote origin found: $REMOTE_URL"



# Enhanced branch selection: show local/remote, allow new branch, show last commit, confirm
function get_branch_list() {
    git for-each-ref --format='%(refname:short)|%(objectname:short)|%(authorname)|%(committerdate:relative)|%(upstream:short)' refs/heads refs/remotes | \
    awk -F'|' '{
        label = ($1 ~ /^origin\//) ? "[remote]" : "[local]";
        printf "%s %s (last: %s, %s)\n", label, $1, $3, $4;
    }'
}

BRANCHES=("Create new branch...")
while IFS= read -r line; do
    BRANCHES+=("$line")
done < <(get_branch_list)

if command -v fzf >/dev/null 2>&1; then
    write_info "Select a branch to push/pull (arrow keys, type to search, or choose 'Create new branch...'):"
    SELECTED=$(printf "%s\n" "${BRANCHES[@]}" | fzf --height 15 --prompt="Branch > ")
else
    write_info "fzf not found. Using basic selection."
    echo "Select a branch to push/pull:"
    select SELECTED in "${BRANCHES[@]}"; do
        if [ -n "$SELECTED" ]; then
            break
        fi
    done
fi

if [ -z "$SELECTED" ]; then
    write_warning "No branch selected. Operation cancelled."
    exit 0
fi

if [[ "$SELECTED" == "Create new branch..." ]]; then
    read -p "Enter new branch name: " NEW_BRANCH
    if [ -z "$NEW_BRANCH" ]; then
        write_warning "No branch name entered. Operation cancelled."
        exit 0
    fi
    git checkout -b "$NEW_BRANCH"
    CURRENT_BRANCH="$NEW_BRANCH"
    write_success "Created and switched to new branch: $CURRENT_BRANCH"
else
    CURRENT_BRANCH=$(echo "$SELECTED" | awk '{print $2}')
    # Confirm before switching
    read -p "You selected '$CURRENT_BRANCH'. Continue? (y/n): " CONFIRM
    if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
        write_warning "Operation cancelled by user."
        exit 0
    fi
    # If not on branch, switch
    CURRENT_ON=$(git branch --show-current)
    if [ "$CURRENT_ON" != "$CURRENT_BRANCH" ]; then
        git checkout "$CURRENT_BRANCH"
        if [ $? -eq 0 ]; then
            write_success "Switched to branch: $CURRENT_BRANCH"
        else
            write_error "Failed to switch branch."
            exit 1
        fi
    fi
fi
write_info "Selected branch: $CURRENT_BRANCH"

# Pull latest changes first
write_progress "Pulling latest changes from remote..."
git pull origin "$CURRENT_BRANCH" --rebase
if [ $? -eq 0 ]; then
    write_success "Successfully pulled latest changes"
else
    write_warning "Pull completed with conflicts or warnings (this is often normal)"
fi

# Push changes
write_progress "Pushing changes to remote repository..."
git push origin "$CURRENT_BRANCH"
if [ $? -eq 0 ]; then
    write_success "Changes pushed successfully to $CURRENT_BRANCH"
else
    write_error "Failed to push changes"
    write_info "You may need to resolve conflicts or check your credentials"
    exit 1
fi

# Show final status
write_header "Git Sync Complete!"
write_success "Repository is now synchronized"
write_info "Commit message: '$MESSAGE'"
write_info "Branch: $CURRENT_BRANCH"
write_info "Remote: $REMOTE_URL"

echo
write_color_message "Recent commits:" "White" "[COMMITS]"
git log --oneline -5 | while read -r line; do
    echo -e "  COMMIT: \033[1;30m$line\033[0m"
done

echo
write_color_message "Git sync completed successfully!" "Green" "[COMPLETE]"
