#!/bin/bash
set -e

# Container not finished with startup/init routines
if [ -e "/startup.flag" ]
# 2: starting - the container is not ready for use yet, but is working correctly
then exit 2
fi

exit 0