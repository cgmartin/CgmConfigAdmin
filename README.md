CgmConfigAdmin
==============
Version 0.0.1 Created by Christopher Martin

Introduction
------------

CgmConfigAdmin is a ZF2 module for managing configuration settings via a web interface.

{Screenshot}

Settings are exposed to the administration panel via a simple configuration file.
Module authors are also able to include their own configuration groups very easily in their
module.config.php file.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)

Features / Goals
----------------
* A configuration setting can be easily configured to have a particular Form input type (Radio, Select, MultiCheckbox, Text, Range,
  etc.) [COMPLETE]
* Preview settings in the administrator's browser before publishing to all users. [COMPLETE]

* View Helper to alert when in Preview Mode [INCOMPLETE]

Installation
------------

### Main Setup

1. Install the [ZfcBase](https://github.com/ZF-Commons/ZfcBase) ZF2 module
   by cloning it into `./vendor/` and enabling it in your
   `application.config.php` file.
2. Clone this project into your `./vendor/` directory and enable it in your
   `application.config.php` file.
3. Copy `./vendor/CgmConfigAdmin/config/cgmconfigadmin.global.php.dist` to
   `./config/autoload/cgmconfigadmin.global.php` and change the values as desired.
4. Import the SQL schema located in `./vendor/CgmConfigAdmin/data/schema.sql`.
5. Navigate to `/config-admin` and try it out.

### Database Adapter Configuration

1. If you do not already have a valid Zend\Db\Adapter\Adapter in your service
   manager configuration, put the following in `./config/autoload/database.local.php`:

        <?php

        $dbParams = array(
            'database'  => 'changeme',
            'username'  => 'changeme',
            'password'  => 'changeme',
            'hostname'  => 'changeme',
        );

        return array(
            'service_manager' => array(
                'factories' => array(
                    'Zend\Db\Adapter\Adapter' => function ($sm) use ($dbParams) {
                        return new Zend\Db\Adapter\Adapter(array(
                            'driver'    => 'pdo',
                            'dsn'       => 'mysql:dbname='.$dbParams['database'].';host='.$dbParams['hostname'],
                            'database'  => $dbParams['database'],
                            'username'  => $dbParams['username'],
                            'password'  => $dbParams['password'],
                            'hostname'  => $dbParams['hostname'],
                        ));
                    },
                ),
            ),
        );

Adding Configuration Groups from other Modules
----------------------------------------------

Simply add a new config group and options for your module and they will be included

    <?php
    // module.config.php
    return array(
        //...
        'cgmconfigadmin' => array(
            'config_groups' => array(
                'mymod' => array('label' => 'My Module Options',  'sort' => 100),
            ),

            'config_options' => array(
                'mymod' => array(
                    'someText'   => 'Some text',
                    'someNumber' => '50',
                    'someSelect' => array('Foo', 'Bar', 'Dev', 'Null'),
                ),
            ),
        ),
    );