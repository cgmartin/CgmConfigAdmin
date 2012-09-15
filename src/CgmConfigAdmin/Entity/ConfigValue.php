<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Entity;

class ConfigValue
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param  string $id
     * @return ConfigValue
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
     * Sets a serialized config value
     *
     * @param  string $value
     * @return ConfigValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Returns a serialized config value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
