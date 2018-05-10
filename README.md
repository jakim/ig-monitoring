**2018.04.18 NOTE:**

**I have restored the basic functions, just run “composer update”.**

**Have fun** :sunglasses:

12.04.2018 NOTE:

~~Instagram has made changes, all public endpoints have stopped working. I will look for a workaround.~~

~~At the moment the tool is dead :confused:~~

## IG Monitoring

[Screenshots](#screenshots)

[FAQ](#faq)

# Version
DEV stage.  **Use at your own risk.**

[![Maintainability](https://api.codeclimate.com/v1/badges/9bbae6907e6cbf039950/maintainability)](https://codeclimate.com/github/jakim/ig-monitoring/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jakim/ig-monitoring/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jakim/ig-monitoring/?branch=master)

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

# Install
- git clone
- composer install

- create database: mysql, utf8mb4
- copy config/db.dist => config/db.php
- `./yii migrate`

- [register google project for Google+ Sign-In](https://developers.google.com/+/web/signin/)
- copy config/authClientCollection.php.dist => config/authClientCollection.php

- [configure worker](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/worker.md)
- create cron hourly for: `./yii stats/update-accounts` and `./yii stats/update-tags`

# Manual
Everything should be added from the command line.
> NOTE: Everything will be slowly moved to the admin panel.

- `./yii proxy/create` (moved)
- `./yii monitoring/account` (moved)
- `./yii monitoring/tag` (moved)
- `./yii help monitoring/account` - displays help for the command

See `./yii` for more commands.


# Requirements
- You need at least one, **WORKING proxy** for accounts and one for tags.
- Works only for public accounts.
- Unix system with root access (not tested on windows)
- Web server (nginx, apache, etc.)
- php >= 7.1
- mysql >= 5.7

# Legal
This code is in no way affiliated with, authorized, maintained, sponsored or endorsed by Instagram or any of its affiliates or subsidiaries.
This is an independent tool. **Use at your own risk.**

# Screenshots
![image](https://user-images.githubusercontent.com/839118/37047660-ee744630-216b-11e8-943a-822a432da725.png)
![image](https://user-images.githubusercontent.com/839118/37047713-18034474-216c-11e8-9123-d17f1543d65f.png)
![dashboard_ig_monitoring](https://user-images.githubusercontent.com/839118/38170151-9680cb8a-357d-11e8-9cf4-b25b75ccbef6.png)
![image](https://user-images.githubusercontent.com/839118/37048055-0b5362f8-216d-11e8-9dab-a82304dd4353.png)
![image](https://user-images.githubusercontent.com/839118/37048109-3372280a-216d-11e8-988d-c825dfe2432c.png)

# FAQ
Why did I build it?

Because I need something that I can quickly change for my needs.

Why is it free?

Because I realized that I like building tools more than using them :)

Is it safe for usage?

You never known, but I’m using this for few months now without any issue.

What do I expect from this share?

New ideas would be great, feel free to create issue and PR. :)
