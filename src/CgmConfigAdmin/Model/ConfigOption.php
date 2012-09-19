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
use Zend\Form\Factory as FormFactory;
use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\ValidatorPluginManager;

class ConfigOption extends AbstractOptions
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
     * @var string
     */
    protected $inputType = 'radio';

    /**
     * @var array|callback
     */
    protected $valueOptions;

    /**
     * @var string|array
     */
    protected $defaultValue;

    /**
     * @var string|array
     */
    protected $value;

    /**
     * @var string
     */
    protected $groupId = 'default';

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @param string|null           $id
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
     * @param  int|string|array $options
     * @return ConfigGroup
     */
    public function setOptions($options)
    {
        if (is_bool($options)) {
            $this->setInputType('radio');
            $this->setDefaultValue(($options) ? '1' : '');
        } elseif (is_numeric($options)) {
            $this->setInputType('number');
            $this->setDefaultValue($options);
        } elseif (is_string($options)) {
            $this->setInputType('text');
            $this->setDefaultValue($options);
        } elseif (is_array($options) && !$this->isAssocArray($options)) {
            $this->setInputType('select');
            $this->setValueOptions($options);
        } else {
            $this->setFromArray($options);
        }
        return $this;
    }

    /**
     * @param $arr
     * @return bool
     */
    protected function isAssocArray($arr)
    {
        return (bool)count(array_filter(array_keys($arr), 'is_string'));
    }

    /**
     * @param  string $id
     * @return ConfigOption
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
     * @return string
     */
    public function getUniqueId()
    {
        return $this->getGroupId() . '_' . $this->getId();
    }

    /**
     * @param  string $label
     * @return ConfigOption
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
     * @param string $inputType
     * @return ConfigOption
     */
    public function setInputType($inputType)
    {
        // TODO: validation for types
        $this->inputType = $inputType;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * @param  array|callback $valueOptions
     * @return ConfigOption
     */
    public function setValueOptions($valueOptions)
    {
        $this->valueOptions = $valueOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (is_callable($this->valueOptions)) {
            $callback = $this->valueOptions;
            $this->valueOptions = $callback($this);
        }
        return $this->valueOptions;
    }

    /**
     * @param array|string $defaultValue
     * @return ConfigOption
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param array|string $value
     * @return ConfigOption
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        if (!isset($this->value)) {
            $this->value = $this->getDefaultValue();
        }
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasValueChanged()
    {
        return ($this->getValue() != $this->getDefaultValue());
    }

    public function resetToDefaultValue()
    {
        $this->setValue(null);
        return $this;
    }

    /**
     * @param string $groupId
     * @return ConfigOption
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param  boolean $required
     * @return ConfigOption
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Prepare default values for form element and input factories
     *
     * @return ConfigOption
     */
    public function prepare()
    {
        $type         = $this->getInputType();
        $defaultValue = $this->getDefaultValue();
        if (null !== ($valueOptions = $this->getValueOptions())) {
            if (!$this->isAssocArray($valueOptions)) {
                $valueOptions = array_combine($valueOptions, $valueOptions);
                $this->setValueOptions($valueOptions);
            }
            if (null === $defaultValue) {
                reset($valueOptions);
                $defaultValue = key($valueOptions);
                $this->setDefaultValue($defaultValue);
            }
        } elseif ('radio' === $type) {
            $valueOptions = array(
                '1' => 'Yes', '' => 'No',
            );
            $this->setValueOptions($valueOptions);
            if (null === $defaultValue) {
                $defaultValue = '';
                $this->setDefaultValue($defaultValue);
            }
        }
        return $this;
    }



    /**
     * @return array
     */
    public function getValueOptionValues($includeEmpty = false)
    {
        $values = array();
        $options = $this->getValueOptions();
        foreach ($options as $key => $optionSpec) {
            if (is_array($optionSpec) && array_key_exists('options', $optionSpec)) {
                foreach ($optionSpec['options'] as $nestedKey => $nestedOptionSpec) {
                    $values[] = is_array($nestedOptionSpec) ? $nestedOptionSpec['value'] : $nestedKey;
                }
            } else {
                $values[] = is_array($optionSpec) ? $optionSpec['value'] : $key;
            }
        }
        if ($includeEmpty && !in_array('', $values)) {
            $values[] = '';
        }
        return $values;
    }
}
