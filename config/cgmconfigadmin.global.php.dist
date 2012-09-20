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

$settings = array(
    'zend_db_adapter'     => 'Zend\Db\Adapter\Adapter',
    'config_values_table' => 'configadminvalues',

    'config_options' => array(
        'site' => $configOptions,
    ),
    'config_groups'  => array(
        'site' => $configGroups,
    ),
);

return array(
    'cgmconfigadmin' => $settings,
    'service_manager' => array(
        'aliases' => array(
            'cgmconfigadmin_zend_db_adapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ),
    ),
);

