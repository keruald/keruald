#!/bin/sh
git diff-tree --no-commit-id --name-only -r HEAD \
    | awk -F/ '$1 !~ /^_/ && NF > 1 { print $1 }' \
    | sort -u
