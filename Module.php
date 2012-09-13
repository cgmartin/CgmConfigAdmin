<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\ModuleRouteListener;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'cgmconfigadmin_service' => 'CgmConfigAdmin\Service\ConfigAdmin',
            ),

            'factories' => array(
                // Configuration options for entire module
                'cgmconfigadmin_module_options' => function($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(
                        isset($config['cgmconfigadmin']) ? $config['cgmconfigadmin'] : array()
                    );
                },

                // Groups of Config Option Definitions
                'cgmconfigadmin_config_groups' => function($sm) {
                    $options = $sm->get('cgmconfigadmin_module_options');
                    $modelFactory = new Model\Factory();
                    return $modelFactory->createConfigGroupsFromModuleOptions($options);
                },

                // Dynamic Config Options Form
                'cgmconfigadmin_form' => function($sm) {
                    $groups = $sm->get('cgmconfigadmin_config_groups');
                    $form = new Form\ConfigOptions($groups);
                    return $form;
                },

            ),
        );
    }

}
