<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Entity;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\ResultSet\HydratingResultSet;

class ConfigValuesMapper extends AbstractDbMapper implements ConfigValuesMapperInterface
{
    protected $tableName  = 'configadminvalues';

    /**
     * @param string $id
     * @return ConfigValues|null
     */
    public function find($id)
    {
        $select = $this->getSelect()
            ->where(array('configvalues_id' => $id));

        $resultSet = $this->select($select);

        if ($resultSet->count()) {
            $configValue = $resultSet->current();
            $this->getEventManager()->trigger('find', $this, array('configValue' => $configValue));
            return $configValue;
        }
        return null;
    }

    /**
     * @param  ConfigValues  $configValues
     * @return ConfigValuesMapper
     */
    public function save($configValues)
    {
        if ($this->find($configValues->getId())) {
            $this->update($configValues, array('configvalues_id' => $configValues->getId()));
        } else {
            $this->insert($configValues);
        }
        return $this;
    }

    /**
     * @param  $table string
     * @return ConfigValuesMapper
     */
    public function setTableName($table)
    {
        $this->tableName = $table;
        return $this;
    }
}
