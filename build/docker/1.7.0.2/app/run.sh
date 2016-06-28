#!/bin/bash
set -e

composer config -g repositories.pkgundertest path ${PACKAGE_PATH}
composer require --prefer-source ${PACKAGE_NAME} @dev

exit

