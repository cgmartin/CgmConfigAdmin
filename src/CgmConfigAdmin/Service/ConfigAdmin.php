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
use CgmConfigAdmin\Form\ConfigOptionsForm;
use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container as SessionContainer;


class ConfigAdmin extends EventProvider implements ServiceManagerAwareInterface
{
    const SAVE_TYPE_PREVIEW = 1;
    const SAVE_TYPE_RESET   = 2;
    const SAVE_TYPE_SAVE    = 3;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var SessionContainer
     */
    protected $session;

    /**
     * @var array
     */
    protected $configGroups = array();

    /**
     * @var ConfigOptionsForm
     */
    protected $configOptionsForm;


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

        $retVal = false;
        $config = $form->getData();
        if (!empty($config['preview'])) {
            // Preview
            $this->getSession()->configValues = $config;
            $retVal = self::SAVE_TYPE_PREVIEW;
        } else if (!empty($config['reset'])) {
            // Reset Preview to Saved Settings
            unset($this->getSession()->configValues);
            $this->resetConfigGroups();
            $retVal = self::SAVE_TYPE_RESET;
        } else if (!empty($config['save'])) {
            // TODO Save data
            $retVal = self::SAVE_TYPE_SAVE;
        } else {
            // TODO throw exception
        }
        //\Zend\Debug\Debug::dump($config);

        return $retVal;
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
     * @return array
     */
    public function getConfigGroups()
    {
        if (!$this->configGroups) {
            $configGroups = $this->getServiceManager()->get('cgmconfigadmin_configGroups');
            $this->setConfigGroups($configGroups);
        }
        return $this->configGroups;
    }

    /**
     * @param  Form $form
     * @return ConfigAdmin
     */
    public function setConfigGroups(array $groups)
    {
        // TODO: Apply datastore values
        $this->applySessionValuesToConfigGroups($groups);

        $this->configGroups = $groups;
        return $this;
    }

    protected function applySessionValuesToConfigGroups(array $groups)
    {
        $configValues = $this->getSession()->configValues;
        foreach ($groups as $id => $group) {
            if (isset($configValues[$id])) {
                $group->setValues($configValues[$id]);
            }
        }
        return $this;
    }

    protected function resetConfigGroups()
    {
        $configGroups = $this->getConfigGroups();
        foreach($configGroups as $group){
            $group->resetToDefaultValues();
        }
        return $this;
    }

    /**
     * @return ConfigOptionsForm
     */
    public function getConfigOptionsForm()
    {
        if (!$this->configOptionsForm) {
            $this->setConfigOptionsForm($this->getServiceManager()->get('cgmconfigadmin_form'));
        }
        return $this->configOptionsForm;
    }

    /**
     * @param  ConfigOptionsForm $form
     * @return ConfigAdmin
     */
    public function setConfigOptionsForm(ConfigOptionsForm $form)
    {
        $form->addConfigGroups($this->getConfigGroups());
        $this->configOptionsForm = $form;
        return $this;
    }

    /**
     * @return SessionContainer
     */
    public function getSession()
    {
        if (!$this->session) {
            $this->setSession($this->getServiceManager()->get('cgmconfigadmin_session'));
        }
        return $this->session;
    }

    /**
     * @param  SessionContainer $session
     * @return ConfigAdmin
     */
    public function setSession(SessionContainer $session)
    {
        $this->session = $session;
        return $this;
    }
}