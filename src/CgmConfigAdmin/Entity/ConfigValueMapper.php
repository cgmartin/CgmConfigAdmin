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

// TODO: implement ConfigValueMapperInterface

class ConfigValueMapper extends AbstractDbMapper
{
    protected $tableName  = 'configvalue';

    /**
     * @return HydratingResultSet
     */
    public function findAll()
    {
        $select = $this->getSelect()
            ->from($this->tableName);

        $resultSet = $this->select($select);
        $this->getEventManager()->trigger('findAll', $this, array('resultSet' => $resultSet));
        return $resultSet;
    }

    public function saveAll(array $configValues)
    {
        // start transaction
        $result = parent::delete('1 = 1');
        foreach ($configValues as $configValue) {
            $result = parent::insert($configValue);
            $configValue->setId($result->getGeneratedValue());
        }
        // end transaction
    }
}
