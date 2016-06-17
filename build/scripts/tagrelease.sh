#!/usr/bin/env bash
# this script tags the software based on the version given in config.xml
# experimental, use at your own risk!
# ./tagrelease.sh Dhl Versenden
set -e

BASEDIR=$(dirname "$0")/../..
VENDOR_NAME="$1"
MODULE_NAME="$2"

CONFIG_FILE=${BASEDIR}/src/app/code/community/${VENDOR_NAME}/${MODULE_NAME}/etc/config.xml
CURRENT_VERSION=`sed -n 's/\s*<version>\(.*\)<\/version>\s*/\1/p' ${CONFIG_FILE}`

echo "Setting new tag ${CURRENT_VERSION}"
git tag ${CURRENT_VERSION}
git push origin ${CURRENT_VERSION}
