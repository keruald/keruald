#!/bin/sh

set -e

# Parses arguments
if [ $# -lt 1 ]; then
    echo "Usage: $0 <TARGET REPOSITORY>" 1>&2
    exit 1
fi
REPO=$1

set -x

# Prepares new main branch for $REPO
git subtree split -P "$REPO" -b "$REPO"

# Merge new subtree split branch into main
git rebase --strategy-option=ours "$REPO" "$REPO"/main

# Push new main
git push "$REPO" "$REPO"/main:main

# Cleans up
git checkout main
git branch -D "$REPO"
