#!/bin/bash
set -e

DATABASE_HOST="$1"
DATABASE_PORT="$2"
DATABASE_PASSWORD="$3"

retry -w 30 -s "mysql -e 'SELECT 1' --user=root --host=$DATABASE_HOST --port=$DATABASE_PORT -p$DATABASE_PASSWORD;" "echo 'Database ready'"

composer config -g repositories.pkgundertest path ${PACKAGE_PATH}
composer require --ignore-platform-reqs --prefer-source ${PACKAGE_NAME} @dev
touch /ready.flag
