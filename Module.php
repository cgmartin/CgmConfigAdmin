<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin;

use CgmConfigAdmin\View\Helper\CgmFlashMessages;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Session\Container as SessionContainer;

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

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'cgmFlashMessages' =>  function($sm) {
                    $plugin = $sm->getServiceLocator()
                        ->get('ControllerPluginManager')
                        ->get('flashMessenger');
                    $helper = new CgmFlashMessages($plugin);
                    return $helper;
                },
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'cgmconfigadmin'         => 'CgmConfigAdmin\Service\ConfigAdmin',
                'cgmconfigadmin_form'    => 'CgmConfigAdmin\Form\ConfigOptionsForm',
            ),

            'factories' => array(
                // Configuration options for entire module
                'cgmconfigadmin_module_options' => function($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(
                        isset($config['cgmconfigadmin']) ? $config['cgmconfigadmin'] : array()
                    );
                },

                // Session container
                'cgmconfigadmin_session' => function($sm) {
                    $session = new SessionContainer('cgmconfigadmin');
                    return $session;
                },

                // Groups of Config Option Definitions
                'cgmconfigadmin_config_groups' => function($sm) {
                    $options = $sm->get('cgmconfigadmin_module_options');
                    $modelFactory = new Model\Factory();
                    return $modelFactory->createConfigGroupsFromModuleOptions($options);
                },

                // Data Mapper for config values
                'cgmconfigadmin_configvalue_mapper' => function ($sm) {
                    /** @var $options Options\ModuleOptions */
                    $options = $sm->get('cgmconfigadmin_module_options');

                    $mapper = new Entity\ConfigValueMapper();
                    $mapper->setTableName($options->getConfigValueTable());
                    $mapper->setDbAdapter($sm->get('cgmconfigadmin_zend_db_adapter'));
                    $mapper->setEntityPrototype(new Entity\ConfigValue);
                    $mapper->setHydrator(new Entity\ConfigValueHydrator());
                    return $mapper;
                },
            ),
        );
    }

}
