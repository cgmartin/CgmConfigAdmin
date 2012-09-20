<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Entity;

class ConfigValues
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $values;

    /**
     * @param  string $id
     * @return ConfigValues
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
     * Sets serialized config values
     *
     * @param  string $values
     * @return ConfigValues
     */
    public function setValues($values)
    {
        $this->value = $values;
        return $this;
    }

    /**
     * Returns a serialized config value
     *
     * @return string
     */
    public function getValues()
    {
        return $this->value;
    }

}
