#!/bin/bash

set -e

# VAULT="$HOME/Sync/ObsidianVault/blog"
# SITE_ROOT="$HOME/Sync/Projects/Octopus"
VAULT="$HOME/Sync/ObsidianVault/blog"
SITE_ROOT="$HOME/Sync/Projects/Octopus"

rsync -av --delete \
  "$VAULT/posts/" \
  "$SITE_ROOT/public/posts/"