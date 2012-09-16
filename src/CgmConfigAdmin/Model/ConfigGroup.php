<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */
namespace CgmConfigAdmin\Model;

use CgmConfigAdmin\Util;
use Zend\Stdlib\AbstractOptions;

class ConfigGroup extends AbstractOptions
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var integer|string
     */
    protected $sort = 0;

    /**
     * @var array
     */
    protected $configOptions = array();

    /**
     * @param string                $id
     * @param null|int|string|array $options
     */
    public function __construct($id = null, $options = null)
    {
        if (isset($id)) {
            $this->setId($id);
        }
        if (isset($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * @param  string|array $options
     * @return ConfigGroup
     */
    public function setOptions($options)
    {
        if (is_string($options)) {
            $this->setLabel($options);
        } else {
            $this->setFromArray($options);
        }
        return $this;
    }

    /**
     * @param  string $id
     * @return ConfigGroup
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $label
     * @return ConfigGroup
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if (!isset($this->label) && isset($this->id)) {
            $this->label = Util::convertIdToLabel($this->id);
        }
        return $this->label;
    }

    /**
     * @param  integer|string $sort
     * @return ConfigGroup
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return integer|string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param  array $configOptions
     * @return ConfigGroup
     */
    public function setConfigOptions($configOptions)
    {
        $this->configOptions = $configOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfigOptions()
    {
        return $this->configOptions;
    }

    /**
     * @param  $configOption ConfigOption
     * @return ConfigGroup
     */
    public function addConfigOption(ConfigOption $configOption)
    {
        $configOption->setGroupId($this->getId());
        $this->configOptions[$configOption->getId()] = $configOption;
        return $this;
    }

    /**
     * @param  $configOption ConfigOption
     * @return ConfigGroup
     */
    public function removeConfigOption(ConfigOption $configOption)
    {
        if (array_key_exists($configOption->getId(), $this->configOptions)) {
            $configOption->setGroupId(null);
            unset($this->configOptions[$configOption->getId()]);
        }
        return $this;
    }

    public function hasConfigOption($id)
    {
        return isset($this->configOptions[$id]);
    }

    public function getConfigOption($id)
    {
        if ($this->hasConfigOption($id)) {
            return $this->configOptions[$id];
        }
        return null;
    }

    public function resetToDefaultValues()
    {
        $options = $this->getConfigOptions();
        foreach ($options as $option) {
            $option->resetToDefaultValue();
        }
        return $this;
    }

    public function setValues(array $values)
    {
        foreach ($this->getConfigOptions() as $id => $option) {
            if (isset($values[$id])) {
                $option->setValue($values[$id]);
            }
        }
        return $this;
    }
}
