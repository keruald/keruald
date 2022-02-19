#!/bin/sh
git diff-tree --no-commit-id --name-only -r HEAD | grep -v -e '_.*' | cut -f1 -d"/" | sort | uniq
