#!/bin/bash

LOCAL_DIR="$(pwd)/../public/"
REMOTE_USER="nobody"
REMOTE_HOST="144.202.73.54"
REMOTE_DIR="/var/www/htdocs/www.benjaminhanna.net"

echo "Deploying '$LOCAL_DIR' to ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_DIR}"

rsync -av \
  -e "ssh -i /Users/benjaminhanna/.ssh/nobody" \
  "${LOCAL_DIR}" \
  "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_DIR}"
