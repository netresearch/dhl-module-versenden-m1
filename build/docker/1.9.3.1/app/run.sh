#!/bin/bash
set -e
touch /startup.flag
composer config -g repositories.pkgundertest path ${PACKAGE_PATH}
composer require --ignore-platform-reqs --prefer-source ${PACKAGE_NAME} @dev
unlink /startup.flag
exit
