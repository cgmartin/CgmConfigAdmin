<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Model;

use CgmConfigAdmin\Options\ModuleOptions;

class Factory
{
    protected $configGroupClassName  = 'CgmConfigAdmin\Model\ConfigGroup';
    protected $configOptionClassName = 'CgmConfigAdmin\Model\ConfigOption';

    /**
     * @param  ModuleOptions $options
     * @return array
     * @throws Exception\DomainException
     */
    public function createConfigGroupsFromModuleOptions(ModuleOptions $options)
    {
        $configOptions = $options->getConfigOptions();
        $configGroups  = $options->getConfigGroups();

        $groups = array();
        foreach ($configGroups as $id => $group) {
            $groups[$id] = new $this->configGroupClassName($id, $group);
        }
        if (empty($groups)) {
            $groups['default'] = new $this->configGroupClassName('default', array(
                'label' => 'Settings',
            ));
        }

        foreach ($configOptions as $groupId => $settingsConfig) {
            if (is_int($groupId)) {
                $groupId = 'default';
            }
            if (!array_key_exists($groupId, $groups)) {
                throw new Exception\DomainException(
                    sprintf('Undefined Group ID (%s)', $groupId)
                );
            }
            foreach ($settingsConfig as $settingId => $settingConfig) {
                $configOption = new $this->configOptionClassName($settingId, $settingConfig);
                $groups[$groupId]->addConfigOption($configOption);
            }
        }

        return $groups;
    }

    /**
     * @param  $class string
     * @return Factory
     */
    public function setConfigGroupClassName($class)
    {
        $this->configGroupClass = $class;
        return $this;
    }

    /**
     * @param  $class string
     * @return Factory
     */
    public function setConfigOptionClassName($class)
    {
        $this->configOptionClass = $class;
        return $this;
    }

}
