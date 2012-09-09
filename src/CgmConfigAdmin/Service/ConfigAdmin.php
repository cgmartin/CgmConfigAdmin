<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace CgmConfigAdmin\Service;

use CgmConfigAdmin\Options\ModuleOptions;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class ConfigAdmin extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    public function saveConfigValues($config)
    {
        // var_export :)
    }

    /**
     * get service options
     *
     * @return ModuleOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->getServiceManager()->get('cgmconfigadmin_module_options'));
        }
        return $this->options;
    }

    /**
     * set service options
     *
     * @param ModuleOptions $options
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return ConfigAdmin
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}