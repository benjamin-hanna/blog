#!/bin/bash

LOCAL_DIR="$(pwd)/../public/"

rsync -av "${LOCAL_DIR}" deploy-server:/var/www/htdocs/www.benjaminhanna.net
