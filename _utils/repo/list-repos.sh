#!/bin/sh
git remote -v | awk '{print $1}' | grep -v origin | sort | uniq
