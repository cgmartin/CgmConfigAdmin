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
use CgmConfigAdmin\Entity\ConfigValue;
use CgmConfigAdmin\Entity\ConfigValueMapperInterface;
use CgmConfigAdmin\Model\ConfigGroup;
use CgmConfigAdmin\Model\ConfigOption;
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
     * @var ConfigValueMapperInterface
     */
    protected $configValueMapper;

    /**
     * @var array
     */
    protected $configGroups = array();

    /**
     * @var ConfigOptionsForm
     */
    protected $configOptionsForm;


    /**
     * @param  $config  array
     * @return bool|int
     * @throws Exception\DomainException
     */
    public function saveConfigValues($config)
    {
        $form = $this->getConfigOptionsForm();
        $form->setData($config);
        if (!$form->isValid()) {
            return false;
        }

        $retVal = false;
        $config = $form->getData();
        if (!empty($config['preview'])) {
            // Preview
            $this->previewConfigValues($config);
            $retVal = self::SAVE_TYPE_PREVIEW;
        } else if (!empty($config['reset'])) {
            $this->resetConfigValues();
            $retVal = self::SAVE_TYPE_RESET;
        } else if (!empty($config['save'])) {
            $this->writeConfigValues($config);
            $retVal = self::SAVE_TYPE_SAVE;
        } else {
            throw new Exception\DomainException(
                'Invalid save type. Must be one of preview, save, or reset'
            );
        }

        return $retVal;
    }

    /**
     * @param $config
     */
    public function previewConfigValues($config)
    {
        $this->getSession()->configValues = $config;
        $configGroups = $this->getConfigGroups();
        $this->applyValuesToConfigGroups($config, $configGroups);
    }

    public function resetConfigValues()
    {
        unset($this->getSession()->configValues);
        $this->resetConfigGroups();
    }

    public function writeConfigValues($config)
    {
        $configGroups = $this->getConfigGroups();
        $this->applyValuesToConfigGroups($config, $configGroups);

        $configValues = array();
        /** @var ConfigGroup $group */
        foreach($configGroups as $group) {
            /** @var ConfigOption $option  */
            foreach ($group->getConfigOptions() as $option) {
                if ($option->hasValueChanged()) {
                    $configValue = new ConfigValue();
                    $configValue
                        ->setId($option->getUniqueId())
                        ->setValue(serialize($option->getValue()));
                    $configValues[] = $configValue;
                }
            }
        }
        if (!empty($configValues)) {
            $this->getConfigValueMapper()->saveAll($configValues);
        }
        unset($this->getSession()->configValues);
    }

    /**
     * @param  $groupId  string
     * @param  $optionId string
     * @return mixed
     */
    public function getConfigValue($groupId, $optionId)
    {
        $configGroups = $this->getConfigGroups();
        if (isset($configGroups[$groupId]) && $configGroups[$groupId]->hasConfigOption($optionId)) {
            return $configGroups[$groupId]->getConfigOption($optionId)->getValue();
        }

        throw new Exception\InvalidArgumentException(
            'Config Value does not exist with the $groupId/$optionId combination'
        );
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
        // Apply from data store
        $dbConfigValues = $this->getConfigValueMapper()->findAll();
        //\Zend\Debug\Debug::dump($configValues); die();
        if ($dbConfigValues->count()) {
            $configValues = array();
            /** @var ConfigValue $configValue */
            foreach ($dbConfigValues as $configValue) {
                $configValues[$configValue->getId()] = unserialize($configValue->getValue());
            }

            /** @var ConfigGroup $group */
            foreach($groups as $group) {
                /** @var ConfigOption $option  */
                foreach ($group->getConfigOptions() as $option) {
                    if (isset($configValues[$option->getUniqueId()])) {
                        $option->setValue($configValues[$option->getUniqueId()]);
                    }
                }
            }
        }

        // Apply from session
        $values = $this->getSession()->configValues;
        if ($values) {
            $this->applyValuesToConfigGroups($values, $groups);
        }

        $this->configGroups = $groups;
        return $this;
    }

    protected function applyValuesToConfigGroups(array $values, array $groups)
    {
        foreach ($groups as $id => $group) {
            if (isset($values[$id])) {
                $group->setValues($values[$id]);
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

    /**
     * @return ConfigValueMapperInterface
     */
    public function getConfigValueMapper()
    {
        if (null === $this->configValueMapper) {
            $this->configValueMapper = $this->getServiceManager()->get('cgmconfigadmin_configvalue_mapper');
        }
        return $this->configValueMapper;
    }

    /**
     * @param  ConfigValueMapperInterface $mapper
     * @return ConfigAdmin
     */
    public function setUserMapper(ConfigValueMapperInterface $mapper)
    {
        $this->configValueMapper = $mapper;
        return $this;
    }
}