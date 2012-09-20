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
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigGroupFactory
{
    /**
     * @param  ServiceLocatorInterface $services
     * @param  ModuleOptions           $options
     * @param  string                  $context
     * @return array
     * @throws Exception\DomainException
     */
    public function createConfigGroups(ServiceLocatorInterface $services, ModuleOptions $options, $context = 'site')
    {
        $configOptions = $options->getConfigOptions();
        $configGroups  = $options->getConfigGroups();

        if (!isset($configOptions[$context])) {
            throw new Exception\DomainException(
                sprintf('Config Options context not found (%s)', $context)
            );
        }
        if (!isset($configGroups[$context])) {
            throw new Exception\DomainException(
                sprintf('Config Groups context not found (%s)', $context)
            );
        }

        $configOptions = $configOptions[$context];
        $configGroups  = $configGroups[$context];

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
