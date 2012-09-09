CgmConfigAdmin
==============
Version 0.0.1 Created by Christopher Martin

Introduction
------------

CgmConfigAdmin is a ZF2 module for managing configuration settings via a web interface.

{Screenshot}

Settings can be exposed to the administration panel via a simple configuration file.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)

Features / Goals
----------------
* A setting can be easily configured to have a particular Form input type (Radio, Select, MultiCheckbox, Text, Range,
  etc.) [IN PROGRESS]
* Preview settings in the administrator's browser before publishing to all users. [IN PROGRESS]

Installation
------------

1. Install the [ZfcBase](https://github.com/ZF-Commons/ZfcBase) ZF2 module
   by cloning it into `./vendor/` and enabling it in your
   `application.config.php` file.
2. Clone this project into your `./vendor/` directory and enable it in your
   `application.config.php` file.
3. Copy `./vendor/CgmConfigAdmin/config/cgmconfigadmin.global.php.dist` to
   `./config/autoload/cgmconfigadmin.global.php` and change the values as desired.
4. Ensure that the `$cgmConfigAdmin_cacheFile` exists and is writeable by your web server.
5. Navigate to `/config-admin` and try it out!


