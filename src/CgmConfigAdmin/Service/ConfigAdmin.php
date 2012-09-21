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
use CgmConfigAdmin\Entity\ConfigValues;
use CgmConfigAdmin\Entity\ConfigValuesMapperInterface;
use CgmConfigAdmin\Model\ConfigGroup;
use CgmConfigAdmin\Model\ConfigOption;
use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container as SessionContainer;


class ConfigAdmin extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $userId;

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
     * @var ConfigValuesMapperInterface
     */
    protected $configValuesMapper;

    /**
     * @var array
     */
    protected $configGroups = array();

    /**
     * @var ConfigOptionsForm
     */
    protected $configOptionsForm;

    /**
     * @var bool
     */
    protected $isPreviewEnabled = true;

    /**
     * @param string      $context Optional the config context (site, user, etc.)
     * @param string|null $userId  Optional for per-user config values
     */
    public function __construct($context = 'site', $userId = null)
    {
        $this->context = $context;
        $this->userId  = $userId;
    }

    /**
     * @param  string $groupId  The group id, or format 'groupName\optionName'
     * @param  string $optionId (optional) The option id
     * @return mixed
     */
    public function getConfigValue($groupId, $optionId = null)
    {
        // Support optional format 'groupName\optionName'
        if (!isset($optionId)) {
            $parts = preg_split('/\//', $groupId);
            list($groupId, $optionId) = $parts;
        }

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
     * @param  array|Traversable $config
     * @return boolean
     */
    public function previewConfigValues($config)
    {
        $form = $this->getConfigOptionsForm();
        $form->setData($config);
        if (!$form->isValid()) {
            return false;
        }

        $config = new \ArrayObject($form->getData());

        $this->getEventManager()->trigger(
            __FUNCTION__, $this,  array('configValues' => $config)
        );

        $contextKey = $this->getContextKey();

        $this->getSession()->$contextKey = $config;
        $configGroups = $this->getConfigGroups();
        $this->applyValuesToConfigGroups($config, $configGroups);

        $this->getEventManager()->trigger(
            __FUNCTION__.'.post', $this, array('configValues' => $config)
        );

        return true;
    }

    /**
     * @return ConfigAdmin
     */
    public function resetConfigValues()
    {
        $contextKey = $this->getContextKey();

        $this->getEventManager()->trigger(
            __FUNCTION__, $this,
            array('configValues' => $this->getSession()->$contextKey)
        );

        unset($this->getSession()->$contextKey);
        $this->resetConfigGroups();

        $this->getEventManager()->trigger(
            __FUNCTION__.'.post', $this, array()
        );

        return $this;
    }

    /**
     * @param  array|Traversable $config
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

        $configValues = new \ArrayObject();
        /** @var ConfigGroup $group */
        foreach($configGroups as $group) {
            /** @var ConfigOption $option  */
            foreach ($group->getConfigOptions() as $option) {
                if ($option->hasValueChanged()) {
                    $configValues[$option->getUniqueId()] = $option->getValue();
                }
            }
        }

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('configValues' => $configValues)
        );

        $contextKey = $this->getContextKey();

        $configValuesRow = new ConfigValues();
        $configValuesRow
            ->setId($contextKey)
            ->setValues(serialize($configValues));

        $this->getConfigValuesMapper()->save($configValuesRow);
        unset($this->getSession()->$contextKey);

        $this->getEventManager()->trigger(
            __FUNCTION__.'.post', $this, array('configValues' => $configValues)
        );
        return true;
    }

    /**
     * @return array
     */
    public function getConfigGroups()
    {
        if (!$this->configGroups) {
            $factory = $this->getServiceManager()->get('cgmconfigadmin_configgroupfactory');
            $groups  = $factory->createConfigGroups(
                $this->getServiceManager(), $this->getOptions(), $this->context
            );

            $contextKey = $this->getContextKey();

            // Apply from data store
            $configValue = $this->getConfigValuesMapper()->find($contextKey);
            //\Zend\Debug\Debug::dump($configValue); die();
            if (!empty($configValue)) {
                $configValues = unserialize($configValue->getValues());

                /** @var ConfigGroup $group */
                foreach($groups as $group) {
                    /** @var ConfigOption $option  */
                    foreach ($group->getConfigOptions() as $option) {
                        $uniqId = $option->getUniqueId();
                        if (isset($configValues[$uniqId])) {
                            $option->setValue($configValues[$uniqId]);
                        }
                    }
                }
            }

            // Apply from session
            $values = $this->getSession()->$contextKey;
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
     * @return string
     */
    protected function getContextKey()
    {
        $key = $this->context;
        if (isset($this->userId)) {
            $key .= "-" . $this->userId;
        }
        return $key;
    }

    /**
     * @param array|\ArrayAccess $values
     * @param array|\Traversable $groups
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
            /** @var ConfigOptionsForm $form */
            $form = $this->getServiceManager()->get('cgmconfigadmin_form');
            $form->setIsPreviewEnabled($this->isPreviewEnabled());
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
     * @return ConfigValuesMapperInterface
     */
    public function getConfigValuesMapper()
    {
        if (null === $this->configValuesMapper) {
            $this->setConfigValuesMapper(
                $this->getServiceManager()->get('cgmconfigadmin_configvalues_mapper')
            );
        }
        return $this->configValuesMapper;
    }

    /**
     * @param  ConfigValuesMapperInterface $mapper
     * @return ConfigAdmin
     */
    public function setConfigValuesMapper(ConfigValuesMapperInterface $mapper)
    {
        $this->configValuesMapper = $mapper;
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
     * @param  ServiceManager $locator
     * @return ConfigAdmin
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPreviewEnabled()
    {
        return $this->isPreviewEnabled;
    }

    /**
     * @param  boolean $locator
     * @return ConfigAdmin
     */
    public function setIsPreviewEnabled($enabled)
    {
        $this->isPreviewEnabled = $enabled;
        return $this;
    }
}