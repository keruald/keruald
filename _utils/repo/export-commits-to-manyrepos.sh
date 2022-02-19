#!/bin/sh
_utils/repo/get-repos-modified-by-last-commit.sh | xargs -n 1 _utils/repo/export-commits-to-manyrepo.sh
