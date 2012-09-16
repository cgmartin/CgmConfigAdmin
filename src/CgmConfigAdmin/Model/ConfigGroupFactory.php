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
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigGroupFactory implements FactoryInterface
{
    /**
     * @param  ModuleOptions $options
     * @return array
     * @throws Exception\DomainException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var $options ModuleOptions */
        $options = $services->get('cgmconfigadmin_module_options');

        $configOptions = $options->getConfigOptions();
        $configGroups  = $options->getConfigGroups();

        $groups = array();
        foreach ($configGroups as $id => $group) {
            $configGroup = $services->get('cgmconfigadmin_configgroup');
            $configGroup->setId($id)->setOptions($group);
            $groups[$id] = $configGroup;
        }
        if (empty($groups)) {
            $configGroup = $services->get('cgmconfigadmin_configgroup');
            $configGroup
                ->setId('default')
                ->setOptions(array(
                    'label' => 'Settings',
                ));
            $groups['default'] = $configGroup;
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
                $configOption = $services->get('cgmconfigadmin_configoption');
                $configOption->setId($settingId)->setOptions($settingConfig);
                $groups[$groupId]->addConfigOption($configOption);
            }
        }

        return $groups;
    }
}
