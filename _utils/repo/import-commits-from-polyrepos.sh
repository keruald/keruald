#!/bin/sh
_utils/repo/list-repos.sh | xargs -I '{}' -n 1 git merge --strategy recursive --strategy-option subtree={}/ {}/main
