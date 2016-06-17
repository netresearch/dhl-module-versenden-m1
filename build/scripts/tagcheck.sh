#!/usr/bin/env bash
# this script checks if the version number from config.xml is still available.
# experimental, use at your own risk!
# ./tagcheck.sh Dhl Versenden
set -e

BASEDIR=$(dirname "$0")/../..
VENDOR_NAME="$1"
MODULE_NAME="$2"

CONFIG_FILE=${BASEDIR}/src/app/code/community/${VENDOR_NAME}/${MODULE_NAME}/etc/config.xml
CURRENT_VERSION=`sed -n 's/\s*<version>\(.*\)<\/version>\s*/\1/p' ${CONFIG_FILE}`

for VERSION in `git tag -l`
do
  if [ ${VERSION} == ${CURRENT_VERSION} ]
  then
    echo "Version $CURRENT_VERSION already exists."
    exit 1
  fi
done

echo "Building new version $CURRENT_VERSION"
