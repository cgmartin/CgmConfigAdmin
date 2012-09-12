<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */
namespace CgmConfigAdmin\Service;

use CgmConfigAdmin\Options\ModuleOptions;
use CgmConfigAdmin\Form\ConfigOptions as ConfigOptionsForm;
use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Form\Form;


class ConfigAdmin extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Form
     */
    protected $configOptionsForm;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @param  $config array()
     * @return bool
     */
    public function saveConfigValues($config)
    {
        //\Zend\Debug\Debug::dump($config);
        $form = $this->getConfigOptionsForm();
        $form->setData($config);
        if (!$form->isValid()) {
            return false;
        }

        $config = $form->getData();
        //\Zend\Debug\Debug::dump($config);

        return true;
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

    /**
     * @return Form
     */
    public function getConfigOptionsForm()
    {
        if (!$this->configOptionsForm) {
            $this->setConfigOptionsForm($this->getServiceManager()->get('cgmconfigadmin_form'));
        }
        return $this->configOptionsForm;
    }

    /**
     * @param  Form $form
     * @return ConfigAdmin
     */
    public function setConfigOptionsForm(Form $form)
    {
        $this->configOptionsForm = $form;
        return $this;
    }
}