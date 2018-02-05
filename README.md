## IG Monitoring

# Version
Early dev stage.  **Use at your own risk.**

# Install
- git clone
- composer install

# Config
- create database: mysql, utf8mb4
- copy config/db.dist => config/db.dist
- register google project for Google Sign-In
- copy config/authClientCollection.php.dist => config/authClientCollection.php
- [configure worker](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/worker.md)
- create cron hourly for: `./yii stats/update-accounts` and `./yii stats/update-tags`

# Manual
Everything should be added from the command line.

- `./yii proxy/create`
- `./yii monitoring/account`
- `./yii monitoring/tag`
- `./yii help monitoring/account` - displays help for the command

See `./yii` for more commands.


# Limitations
You need at least one, **WORKING proxy** for accounts and one for tags.

# Legal
This code is in no way affiliated with, authorized, maintained, sponsored or endorsed by Instagram or any of its affiliates or subsidiaries.
This is an independent tool. **Use at your own risk.**

