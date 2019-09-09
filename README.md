# IG Monitoring Cloud

Let me introduce you to a cloud version of a system I'm currently working on.
Perhaps it will answer your needs. That will be more featured version of the system you know from GitHub, with more extensive tag analytics and account analytics expanded with locations. Additionally it'll have various permission levels, so you can create an account for a Client with stats available there. And that's just for a starter :)

## [Get your access here](https://igmonitoring.com/)

[Versions comparison](https://igmonitoring.com/versions-comparison)

# IG Monitoring - Free version

[Screenshots](#screenshots)

[Free DEMO](https://demo.igmonitoring.com)

[Cloud Free Trial](https://app.igmonitoring.com/admin/auth/register)

[Account statistics](https://igmonitoring.com/versions-comparison)

[Tag statistics](https://igmonitoring.com/versions-comparison)

[FAQ](#faq)

[PREMIUM SUPPORT](#free-version-premium-support)

[Versions comparison](https://igmonitoring.com/versions-comparison)

# Version
BETA stage.  **Use at your own risk.**

Branch 0.9 is the most stable.

[![Maintainability](https://api.codeclimate.com/v1/badges/9bbae6907e6cbf039950/maintainability)](https://codeclimate.com/github/jakim/ig-monitoring/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jakim/ig-monitoring/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jakim/ig-monitoring/?branch=master)
[![Build Status](https://travis-ci.org/jakim/ig-monitoring.svg?branch=master)](https://travis-ci.org/jakim/ig-monitoring)

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)


# Installation

## Before you start

You need at least one, **WORKING proxy**.

You need a server (vps will be ok) with linux, root access and configured LEMP, that is:

- php minimum 7.2
- latest nginx, recommended server configuration https://www.yiiframework.com/doc/guide/2.0/en/start-installation#configuring-web-servers
- mysql minimum 5.7 (or the appropriate mariadb, e.g. 10.3)
- [installation instructions on Debian](https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-debian-8) (there is no need for a firewall ;)

## System installation (terminal)
- create database: mysql, utf8mb4
- run `git clone https://github.com/jakim/ig-monitoring.git`
- run `cd ig-monitoring` (you will enter the project catalog)
- [download composer](https://getcomposer.org/download/)
- run `php composer.phar install`
- run `chmod 0777 runtime`
- run `chmod 0777 web/assets`
- run `chmod 0777 web/uploads`
- copy `config/db.dist` => `config/db.php` and enter the access data to the created database
- run `php yii migrate` (tables in the database should be created)
- run `php yii admin/dictionaries` 

## Configure google sign-in
- go to: https://console.developers.google.com and create a new project
- enable API: Google+ API
- add oAuth login credentials (type: web application)
- add authorized redirect url `YOUR_DOMAIN/admin/auth/auth?authclient=google`
- copy `config/authClientCollection.php.dist` => `config/authClientCollection.php` and enter `clientId`, `clientSecret` and `redirectUrl` as above

## Worker configuration (data refreshing)
- install the `supervisord`, [the method of installation depends on the system](http://supervisord.org/installing.html#installing-a-distribution-package)
- add configuration according to https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/worker.md (in Debian 8 and 9: `/etc/supervisor/conf.d/ig_monitoring.conf`)
- change in the configuration:
    * `user` => `nginx`
    * `numprocs` => `2` is enough (I recommend twice less than the number of proxy and a number equal to the number of processor cores/threads)
    * I suggest `stdout_logfile` to be set to the project directory, ie `PROJECT_FULL_PATH/runtime/logs/supervisor.log`
- run `supervisord`
- add [cron hourly](https://crontab.guru/every-hour) for `php /PROJECT_FULL_PATH/yii stats/update-accounts` and `php /PROJECT_FULL_PATH/yii stats/update-tags`

## Adding and activation of the system user
- try to log in, if everything goes well, you'll see an "inactive account message"
- run the command `php yii user/activate 'YOUR_GOOGLE_EMAIL'`
- log in again

## Next steps
- add a few accounts and tags
- enjoy the system 
- write a review 
- tell your friends about the system
- star the project on github :)

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

- Because I need something that I can quickly change for my needs.

Why is it free?

- Because I realized that I like building tools more than using them :)

Is it safe for usage?

- You never known, but I’m using this for few months now without any issue.

Do I need to enter my Instagram login and password?

- No, the system is based on publicly available data, so you do not have to provide any sensitive data.

Will the system harm my accounts?

- No, everyone can monitor public accounts.

Why did you build two versions?

- I needed a system to monitor accounts (now it's several thousands), but I enjoy software development more than accounts maintenance, which is why the free version was created. The cloud version was created as a response to all users problems with the installation and maintenance of the free version.

What do I expect from this share?

- New ideas would be great, feel free to create issue and PR. :)


# Free Version PREMIUM SUPPORT

If you do not know how to get into the installation, I can do it for you. As part of the support, I offer:
- installation of a www server
- database installation
- script configuration
- free, good ssl certificate (letsencrypt)
- script modifications, according to requirements
- other software integration 

# Troubleshooting
**Error: redirect_uri_mismatch**

https://github.com/yiisoft/yii2-authclient/issues/241

**'SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes**

You probably have a low database version, make sure your system meets the [requirements](#before-you-start)

