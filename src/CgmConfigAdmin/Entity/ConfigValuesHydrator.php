<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;

class ConfigValuesHydrator extends ClassMethods
{
    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        if (!$object instanceof ConfigValues) {
            throw new Exception\InvalidArgumentException(
                '$object must be an instance of CgmConfigAdmin\Entity\ConfigValues'
            );
        }
        /* @var $object ConfigValues*/
        $data = parent::extract($object);
        $data = $this->mapField('id',     'configvalues_id', $data);
        $data = $this->mapField('values', 'configvalues',    $data);
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     * @return ConfigValues
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof ConfigValues) {
            throw new Exception\InvalidArgumentException(
                '$object must be an instance of CgmConfigAdmin\Entity\ConfigValues'
            );
        }
        $data = $this->mapField('configvalues_id', 'id',     $data);
        $data = $this->mapField('configvalues',    'values', $data);
        return parent::hydrate($data, $object);
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}
