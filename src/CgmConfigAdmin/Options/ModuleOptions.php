<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * @var string
     */
    protected $cacheDir = './data/cache';

    /**
     * @var array
     */
    protected $configOptions = array();

    /**
     * @var array
     */
    protected $configGroups = array();

    /**
     * @param  string $cacheDir
     * @return ModuleOptions
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param  array $configOptions
     * @return ModuleOptions
     */
    public function setConfigOptions($configOptions)
    {
        foreach ($configOptions as $k => $configOption) {
            if (! $configOption instanceof ConfigOption) {
                $configOptions[$k] = new ConfigOption($configOption);
            }
        }
        $this->configOptions = $configOptions;
        return $this;
    }

    /**
     * @return array of ConfigOption
     */
    public function getConfigOptions()
    {
        return $this->configOptions;
    }

    /**
     * @param  array $configGroups
     * @return ModuleOptions
     */
    public function setConfigGroups($configGroups)
    {
        $this->configGroups = $configGroups;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfigGroups()
    {
        return $this->configGroups;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        if (!empty($array['config_options'])) {
            foreach ($array['config_options'] as $k => $configOption) {
                if ($configOption instanceof AbstractOptions) {
                    $array['config_options'][$k] = $configOption->toArray();
                }
            }
        }
        return $array;
    }
}
