CgmConfigAdmin
==============
Version 1.0.0 Created by Christopher Martin

Introduction
------------

CgmConfigAdmin is a ZF2 module for managing configuration settings via a single web page.

![CgmConfigAdmin example screenshot](http://grab.by/g6Cg)

Settings are exposed to the administration panel via a simple configuration format.

Module authors can also easily include their own configuration groups right from their
module.config.php file.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [ZfcBase](https://github.com/ZF-Commons/ZfcBase) (latest master)

Features / Goals
----------------
* Settings can be easily configured for a particular Form input type
  (Radio, Select, MultiCheckbox, Text, Range, etc.)
* Preview settings in the administrator's browser before publishing to all users.
* Multiple rendering options for the settings form. (Two included form view helpers: Fieldsets and Accordian)
* Twitter Bootstrap v2 UI classes (All but error messages are styled) [IN PROGRESS]
* Tooltip support [INCOMPLETE]
* View Helper to alert when in Preview Mode [INCOMPLETE]
* Integration with [ZfcAdmin](https://github.com/ZF-Commons/RFC/wiki/RFC:-ZfcAdmin) [INCOMPLETE]
* Doctrine support [INCOMPLETE]
* Table of Contents view helper with Scrollspy (for long pages of settings) [INCOMPLETE]

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

### Post-Install

Protect the `/config-admin` route with an authorization module, such as
[BjyAuthorize](https://github.com/bjyoungblood/BjyAuthorize).

### Database Adapter Configuration

1. If you do not already have a valid Zend\Db\Adapter\Adapter in your service
   manager configuration, put the following in `./config/autoload/database.local.php`:

   ```php
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
   ```

Configuring custom settings
---------------------------

Example:
```php
<?php
//
// Config Groupings
//
$configGroups = array(
    'group1' => array('label' => 'Simple Options',  'sort' => 1),
    'group2' => array('label' => 'Complex Options', 'sort' => 2),
    'group3' => array('label' => 'Multi Options',   'sort' => 3),
);

//
// Config Options
//
$configOptions = array(

    'group1' => array(
        //
        // Simple Options
        //
        // Key will be automatically converted to a label
        //
        'useCamelCase'    => true,
        'or-dashes'       => false,
        'or_underscores'  => true,
        'simpleText'      => 'Some text',
        'simpleNumber'    => '50',
        'simpleSelect'    => array('Foo', 'Bar', 'Dev', 'Null'),
    ),

    'group2' => array(
        //
        // Complex Options examples
        //
        'boolOption' => array(
            'input_type'    => 'radio',
            'label'         => 'Boolean Option',
            'value_options' => array('1' => 'True', '' => 'False'),
            'default_value' => false,
        ),

        'textOption' => array(
            'input_type'    => 'text',
            'label'         => 'Text Option',
            'default_value' => 'My Site',
        ),

        'numberOption' => array(
            'input_type'    => 'number',
            'label'         => 'Number Option',
            'default_value' => '10',
        ),
    ),

    'group3' => array(
        //
        // Complex Multi-Options examples
        //
        'multiCheckboxOption' => array(
            'input_type'    => 'multicheckbox',
            'label'         => 'MultiCheckbox Option',
            'value_options' => array('Foo', 'Bar', 'Dev', 'Null'),
            'default_value' => array('Bar', 'Dev'),
        ),

        'radioOption' => array(
            'label'         => 'Radio Option',
            'input_type'    => 'radio',
            'value_options' => array('Foo', 'Bar', 'Dev', 'Null'),
            'default_value' => 'Bar',
        ),

        'selectOption' => array(
            'label'         => 'Select Option',
            'input_type'    => 'select',
            'value_options' => array('Spring', 'Summer', 'Fall', 'Winter'),
            'default_value' => 'Fall',
        ),
    ),
);
```

Adding Configuration Groups from other Modules
----------------------------------------------

Simply add a new config group and options for your module and they will be included

```php
<?php
// module.config.php
return array(
    //...
    'cgmconfigadmin' => array(
        'config_groups' => array(
            'mymod' => array('label' => 'My Module Options',  'sort' => -100),
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
```

Usage
-----

To get a setting's value in your project:

```php
<?php
$settingValue = $sm->get('cgmconfigadmin')->getConfigValue('groupid', 'optionid');
```

`CgmConfigAdmin\Service\ConfigAdmin` is registered in the Service Manager under the alias `cgmconfigadmin`.

