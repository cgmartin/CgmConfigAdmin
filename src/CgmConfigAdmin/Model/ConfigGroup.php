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
     * @var array
     */
    protected $configOptions = array();

    /**
     * @param string                $id
     * @param null|int|string|array $options
     */
    public function __construct($id, $options = null)
    {
        $this->setId($id);
        parent::__construct($options);
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
}
