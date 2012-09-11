<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'CgmConfigAdmin_ConfigOptionsController' => 'CgmConfigAdmin\Controller\ConfigOptionsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'cgmconfigadmin' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/config-admin',
                    'defaults' => array(
                        'controller' => 'CgmConfigAdmin_ConfigOptionsController',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'CgmConfigAdmin' => __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'cgmconfigadminaccordionform' => 'CgmConfigAdmin\View\Helper\CgmConfigAdminAccordionForm',
            'cgmconfigadminfieldsetform'  => 'CgmConfigAdmin\View\Helper\CgmConfigAdminFieldsetForm',
        ),
    ),
);
