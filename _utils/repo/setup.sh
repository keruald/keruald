#!/bin/sh

REPOSITORIES_HOST="${REPOSITORIES_HOST:-ssh://vcs@devcentral.nasqueron.org:5022/source}"

###
### Remotes to manyrepo
###

REPOS=$(find . -maxdepth 1 -type d | grep -v vendor | sed 's_./__' | grep -e '^[a-z].*')

for repo in $REPOS; do
    git remote | grep -qe "^$repo$" || git remote add "$repo" "$REPOSITORIES_HOST/$repo"
done
