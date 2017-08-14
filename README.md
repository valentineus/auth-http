# HTTP Basic Authentication
[![Build Status](https://travis-ci.org/valentineus/auth-http.svg?branch=master)](https://travis-ci.org/valentineus/auth-http)

Adds Basic Authentication Moodle.

The plugin is designed to work in the environment of Moodle 3.2+.

It is worthwhile to understand the motives and reasons before installing the plug-in, because:
 * You can not login to the authorization page.
Consequently, the ability to register and restore the password for users will be lost.
 * HTTP Basic Authentication is the least secure authorization system, because authorization keys are transmitted in clear text.
Use a secure HTTPS protocol to protect user information.

## Build
The script `build.sh` collects the final package for installation in Moodle.
```bash
/bin/sh ./build.sh
```

## License
[MIT](LICENSE.md).
Copyright (c)
[Valentin Popov](mailto:info@valentineus.link).