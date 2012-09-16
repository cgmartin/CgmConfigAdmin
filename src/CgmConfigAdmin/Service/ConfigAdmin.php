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
     * @param  $groupId  string
     * @param  $optionId string
     * @return mixed
     */
    public function getConfigValue($groupId, $optionId)
    {
        $configGroups = $this->getConfigGroups();
        if (isset($configGroups[$groupId])
            && $configGroups[$groupId]->hasConfigOption($optionId)
        ) {
            return $configGroups[$groupId]
                ->getConfigOption($optionId)
                ->prepare()->getValue();
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Config Value does not exist with the $groupId/$optionId combination (%s/%s)',
            $groupId, $optionId
        ));
    }

    /**
     * @param  $config array
     * @return boolean
     */
    public function previewConfigValues($config)
    {
        $form = $this->getConfigOptionsForm();
        $form->setData($config);
        if (!$form->isValid()) {
            return false;
        }

        $config = $form->getData();
        $this->getSession()->configValues = $config;
        $configGroups = $this->getConfigGroups();
        $this->applyValuesToConfigGroups($config, $configGroups);
        return true;
    }

    /**
     * @return ConfigAdmin
     */
    public function resetConfigValues()
    {
        unset($this->getSession()->configValues);
        $this->resetConfigGroups();
        return $this;
    }

    /**
     * @param  $config array
     * @return boolean
     */
    public function saveConfigValues($config)
    {
        $form = $this->getConfigOptionsForm();
        $form->setData($config);
        if (!$form->isValid()) {
            return false;
        }

        $config = $form->getData();
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
        return true;
    }

    /**
     * @return array
     */
    public function getConfigGroups()
    {
        if (!$this->configGroups) {
            $groups = $this->getServiceManager()->get('cgmconfigadmin_config_groups');

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

            $this->setConfigGroups($groups);
        }
        return $this->configGroups;
    }

    /**
     * @param  array $groups
     * @return ConfigAdmin
     */
    public function setConfigGroups($groups)
    {
        $this->configGroups = $groups;
        return $this;
    }

    /**
     * @param array $values
     * @param array $groups
     *
     * @return ConfigAdmin
     */
    protected function applyValuesToConfigGroups($values, $groups)
    {
        /** @var $group ConfigGroup */
        foreach ($groups as $id => $group) {
            if (isset($values[$id])) {
                $group->setValues($values[$id]);
            }
        }
        return $this;
    }

    /**
     * @return ConfigAdmin
     */
    protected function resetConfigGroups()
    {
        $configGroups = $this->getConfigGroups();
        /** @var $group ConfigGroup */
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
            $form = $this->getServiceManager()->get('cgmconfigadmin_form');
            $form->addConfigGroups($this->getConfigGroups());
            $this->setConfigOptionsForm($form);
        }
        return $this->configOptionsForm;
    }

    /**
     * @param  ConfigOptionsForm $form
     * @return ConfigAdmin
     */
    public function setConfigOptionsForm(ConfigOptionsForm $form)
    {

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