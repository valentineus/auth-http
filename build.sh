#!/bin/sh
# Author:       Valentin Popov
# Email:        info@valentineus.link
# Date:         2017-08-14
# Usage:        /bin/sh build.sh
# Description:  Build the final package for installation in Moodle.

# Updating the Environment
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export PATH="$PATH:/usr/local/scripts"

# Build the package
cd ..
zip -9 -r auth-http.zip auth-http  \
        -x "auth-http/.git*"       \
        -x "auth-http/.travis.yml" \
        -x "auth-http/build.sh"

# End of work
exit 0