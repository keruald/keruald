#!/bin/sh

set -e

# Parse arguments
if [ $# -lt 1 ]; then
    echo "Usage: $0 <TARGET REPOSITORY>" 1>&2
    exit 1
fi
REPO=$1

set -x

# Ensure we've up-to-date information
git fetch --all

# Prepare new main branch for $REPO
git subtree split -P "$REPO" -b "$REPO"

# Merge new subtree split branch into main
git fetch $REPO main
git rebase --strategy-option=ours "$REPO" "$REPO"/main

git checkout $REPO
git rebase --strategy-option=ours "$REPO"/main

# Push new main
git push "$REPO" "$REPO":main

# Clean up
git checkout main
git branch -D "$REPO"
