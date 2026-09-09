#!/bin/bash
set -e
SITE_ROOT="$HOME/Sync/Projects/Octopus"

usage() {
    echo "Usage: $0 [-b] [-d] [-h]"
    echo "  -b   	  Build the site infrastructure"
    echo "  -d        Deploy the site files to the remote server"
    echo "  -s        Sync http.conf files from remote server"   
    echo "  -v        Verbose"
    echo "  -h        Show help message"
    exit 1
}

if [[ $# -eq 0 ]]; then
	php "$SITE_ROOT/build/build.php"
	bash "$SITE_ROOT/build/tidy.sh" "$SITE_ROOT/public"
	bash "$SITE_ROOT/build/deploy.sh" "$SITE_ROOT/public"
	bash "$SITE_ROOT/build/sync-httpd-conf.sh" "$SITE_ROOT/httpd"
    exit 0
fi

verbose=""

while getopts "bdvh" opt; do
    case "$opt" in
        b) php "$SITE_ROOT/build/build.php" $verbose
           bash "$SITE_ROOT/build/tidy.sh" "$SITE_ROOT/public" ;;
        d) bash "$SITE_ROOT/build/deploy.sh" "$SITE_ROOT/public" ;;
        s) bash "$SITE_ROOT/build/sync-httpd-conf.sh" "$SITE_ROOT/httpd" ;;
        v) verbose="-v" ;;
        h) usage ;;
        \?) usage ;;
    esac
done