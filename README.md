## IG Monitoring

[Screenshots](#screenshots)

[Account statistics](#account-statistics)

[FAQ](#faq)

[PREMIUM SUPPORT](#premium-support)

# Version
DEV stage.  **Use at your own risk.**

[![Maintainability](https://api.codeclimate.com/v1/badges/9bbae6907e6cbf039950/maintainability)](https://codeclimate.com/github/jakim/ig-monitoring/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jakim/ig-monitoring/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jakim/ig-monitoring/?branch=master)
[![Build Status](https://travis-ci.org/jakim/ig-monitoring.svg?branch=master)](https://travis-ci.org/jakim/ig-monitoring)

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
A few things can be done only from the command line.
> NOTE: Everything will be slowly moved to the admin panel.

- `./yii user/activate  ID - activation of the user account
- `./yii - list of all commands
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

## Account statistics

- total number of “followed by”
- total number of “follows”
- total number of media
- engangment rate (calculated for last 10 posts)
- chart for last month’s data
- daily change
- monthly change
- a public link to view statistics, e.g. for the client
- tags linked to account
- accounts linked to account

## Tag statistics

- total number of media
- total number of likes from top 9 posts
- min likes from top 9 posts
- max likes from top 9 posts
- total number of comments from top 9 posts
- min comments from top 9 posts
- max comments from top 9 posts

# FAQ
Why did I build it?

Because I need something that I can quickly change for my needs.

Why is it free?

Because I realized that I like building tools more than using them :)

Is it safe for usage?

You never known, but I’m using this for few months now without any issue.

What do I expect from this share?

New ideas would be great, feel free to create issue and PR. :)

# PREMIUM SUPPORT

If you do not know how to get into the installation, I can do it for you. As part of the support, I offer:
- installation of a www server
- database installation
- script configuration
- free, good ssl certificate (letsencrypt)
- script modifications, according to requirements
- other software integration 

