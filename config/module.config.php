<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'CgmConfigAdmin_ConfigOptionsController' => 'CgmConfigAdmin\Controller\ConfigOptionsController',
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
