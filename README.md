CgmConfigAdmin
==============

[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

Version 1.2.1 Created by Christopher Martin

Introduction
------------

Need to give clients access to website configuration settings?

CgmConfigAdmin is a ZF2 module for managing site-wide settings via a single web page.

![CgmConfigAdmin example screenshot](https://github.com/cgmartin/CgmConfigAdmin/raw/master/screenshot.png)

Settings are exposed to the administration panel via a simple configuration format.

Module authors can also easily include their own specific configuration settings right
from their module.config.php file.

### UPDATES IN 1.2.1

Please see [CHANGELOG.md](CHANGELOG.md).

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2)
* [ZfcBase](https://github.com/ZF-Commons/ZfcBase)
* A Database or Key/Value store

Installation
------------

### Composer / Packagist
```
% composer.phar require cgm/config-admin
Please provide a version constraint for the cgm/config-admin requirement: dev-master
```

### Main Setup

1. Install the [ZfcBase](https://github.com/ZF-Commons/ZfcBase) ZF2 module
   by cloning it into `./vendor/` and enabling `ZfcBase` in your `application.config.php` file.
2. Clone this project into your `./vendor/` directory and enable `CgmConfigAdmin` in your
   `application.config.php` file.
3. Copy `./vendor/CgmConfigAdmin/config/cgmconfigadmin.global.php.dist` to
   `./config/autoload/cgmconfigadmin.global.php` and change the values as desired.
4. Import the SQL schema located in `./vendor/CgmConfigAdmin/data/schema.sql`.
5. Navigate to `/config-admin` and try it out.

### Post-Install

Protect the `/config-admin` route with an authorization module, such as
[ZfcRbac](https://github.com/ZF-Commons/ZfcRbac) or
[BjyAuthorize](https://github.com/bjyoungblood/BjyAuthorize). The route can
be changed in the `./config/autoload/cgmconfigadmin.global.php` file.

### Database Adapter Configuration

If you do not already have a valid Zend\Db\Adapter\Adapter in your service
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

See the [Zend\Db\Adapter](http://framework.zend.com/manual/2.0/en/modules/zend.db.adapter.html)
documentation for more info on how to configure the adapter for your specific database.

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
            'required'      => true,        // options are not required by default
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
            'value_options' => function ($configOption) {
                // Callbacks can be used to feed options
                return array('Foo', 'Bar', 'Dev', 'Null');
            },
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
            'site' => array(
                'mymod' => array('label' => 'My Module Options',  'sort' => -100),
            ),
        ),

        'config_options' => array(
            'site' => array(
                'mymod' => array(
                    'someText'   => 'Some text',
                    'someNumber' => '50',
                    'someSelect' => array('Foo', 'Bar', 'Dev', 'Null'),
                ),
            ),
        ),
    ),
);
```

Usage
-----

To retrieve a config value:

```php
<?php
$settingValue = $sm->get('cgmconfigadmin')->getConfigValue('groupId', 'optionId');
// ..or..
$settingValue = $sm->get('cgmconfigadmin')->getConfigValue('groupId/optionId');
```

An instance of the `CgmConfigAdmin\Service\ConfigAdmin` service is registered in the Service Manager
under the alias `cgmconfigadmin`.

Events
------

Events below are emitted from the `CgmConfigAdmin\Service\ConfigAdmin` service:

#### Previewing Config Values

`previewConfigValues` : Before preview values are saved in the session.

* Param `configValues` (ArrayObject) List of config values from form.

`previewConfigValues.post` : After preview values are saved in the session.

* Param `configValues` (ArrayObject) List of config values saved in session.


#### Resetting Config Values

`resetConfigValues` : Before the previewed config values are reset.

* Param `configValues` (ArrayObject) The current list config values in the session.

`resetConfigValues.post` : After the previewed config values are reset.

#### Saving Config Values

`saveConfigValues` : Before the changed list of config values are saved.

* Param `configValues` (ArrayObject) The changed list of config values to be saved.

`saveConfigValues.post` : After the config values have been saved.

* Param `configValues` (ArrayObject) The saved list of config values.


### To attach event listeners:

```php
public function onBootstrap($e)
{
    $events = $e->getApplication()->getEventManager()->getSharedManager();
    $events->attach('CgmConfigAdmin\Service\ConfigAdmin', 'previewConfigValues', function($e) {
        $configAdminService = $e->getTarget();
        $configValues = $e->getParam('configValues');
        // Do what you will...
    });
    $events->attach('CgmConfigAdmin\Service\ConfigAdmin','previewConfigValues.post', function($e) {
        $configAdminService = $e->getTarget();
        $configValues = $e->getParam('configValues');
        // Do what you will...
    });
}
```
