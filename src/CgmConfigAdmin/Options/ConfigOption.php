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
use Zend\Form\Factory as FormFactory;
use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\ValidatorPluginManager;

class ConfigOption extends AbstractOptions
{
    protected static $elementMappings = array(
        'radio'         => 'Zend\Form\Element\Radio',
        'select'        => 'Zend\Form\Element\Select',
        'multicheckbox' => 'Zend\Form\Element\MultiCheckbox',
        'text'          => 'Zend\Form\Element\Text',
        'number'        => 'Zend\Form\Element\Number',
    );

    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

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
     * @var array
     */
    protected $valueOptions;

    /**
     * @var string|array
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $group = 'default';

    /**
     * @var InArrayValidator
     */
    protected $inArrayValidator;

    /**
     * @var ExplodeValidator
     */
    protected $explodeValidator;

    /**
     * @var ValidatorPluginManager
     */
    protected $validatorPluginManager;

    /**
     * @static
     * @param array $mappings
     */
    public static function setElementMappings(array $mappings)
    {
        self::$elementMappings = $mappings;
    }

    /**
     * @static
     * @return array
     */
    public static function getElementMappings()
    {
        return self::$elementMappings;
    }

    /**
     * @param string                $id
     * @param null|int|string|array $options
     */
    public function __construct($id, $options = null)
    {
        $this->setId($id);
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
            parent::__construct($options);
        }
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
            // Convert camelCase or dash-string or under_score ids to a
            // human readable label for convenience
            $label = preg_replace(
                array(
                    '/(?<=[^A-Z])([A-Z])/',
                    '/(?<=[^0-9])([0-9])/',
                ),
                ' $0',
                $this->id
            );
            $label = preg_replace_callback(
                '/[\-_]([a-zA-Z0-9])/',
                function ($matches) {
                    return ' ' . strtoupper($matches[1]);
                },
                $label
            );
            $this->label = ucwords($label);
        }
        return $this->label;
    }

    /**
     * @param string $inputType
     * @return ConfigOption
     */
    public function setInputType($inputType)
    {
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
     * @param array $valueOptions
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
     * @param string $group
     * @return ConfigOption
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Prepare default values for form element and input factories
     *
     * @return ConfigOption
     */
    public function prepareForForm()
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
     * Create a form element spec from internal data
     *
     * @return array
     */
    public function createFormElementSpec()
    {
        $elementSpec = array();

        $type = $this->getInputType();
        $elementSpec['type'] = (array_key_exists($type, self::$elementMappings))
            ? self::$elementMappings[$type] : $type;

        $elementSpec['name']             = $this->getId();
        $elementSpec['options']['label'] = $this->getLabel();

        // Default Value
        if (null !== ($defaultValue = $this->getDefaultValue())) {
            $elementSpec['attributes']['value'] = $defaultValue;
        }

        // Value Options
        if (null !== ($valueOptions = $this->getValueOptions())) {
            $elementSpec['options']['value_options'] = $valueOptions;
        }

        return $elementSpec;
    }

    /**
     * Create an input filter spec from internal data
     *
     * @return array
     */
    public function createInputFilterSpec()
    {
        $inputSpec = array();

        $type = $this->getInputType();

        $inputSpec['name']        = $this->getId();
        $inputSpec['required']    = false;
        $inputSpec['allow_empty'] = true;

        $validators = array();
        $filters    = array();
        switch ($type) {
            case 'radio':
            case 'select':
                $validators[] = $this->getInArrayValidator(false);
                break;
            case 'multicheckbox':
                $validators[] = $this->getExplodeValidator(true);
                break;
            //case 'text':
            case 'number':
                $filters[]    = array('name' => 'Zend\Filter\StringTrim');
                $validators[] = array(
                    'name'    => 'float',
                    'options' => array('locale' => 'en_US'),
                );
            break;
        }
        if (!empty($filters)) {
            $inputSpec['filters'] = $filters;
        }
        if (!empty($validators)) {
            $inputSpec['validators'] = $validators;
        }

        return $inputSpec;
    }

    /**
     * @return array
     */
    protected function getValueOptionValues($includeEmpty = false)
    {
        $values = array();
        if ($includeEmpty) {
            $values[] = '';
        }
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
        return $values;
    }

    /**
     * @return InArrayValidator
     */
    public function getInArrayValidator($includeEmpty = false)
    {
        if (!isset($this->inArrayValidator)) {
            $this->setInArrayValidator(
                $this->getValidatorPluginManager()->get('inarray')
            );
        }

        $this->inArrayValidator->setHaystack($this->getValueOptionValues($includeEmpty));
        return $this->inArrayValidator;
    }

    /**
     * @param  InArrayValidator $validator
     * @return ConfigOption
     */
    public function setInArrayValidator(InArrayValidator $validator)
    {
        $this->inArrayValidator = $validator;
        return $this;
    }

    /**
     * @return ExplodeValidator
     */
    public function getExplodeValidator($includeEmpty = false)
    {
        if (!isset($this->explodeValidator)) {
            $this->setExplodeValidator(
                $this->getValidatorPluginManager()->get('explode')
            );
        }

        $this->explodeValidator
            ->setValidator($this->getInArrayValidator($includeEmpty))
            ->setValueDelimiter(null); // skip explode if only one value

        return $this->explodeValidator;
    }

    /**
     * @param  ExplodeValidator $validator
     * @return ConfigOption
     */
    public function setExplodeValidator(ExplodeValidator $validator)
    {
        $this->explodeValidator = $validator;
        return $this;
    }

    public function getValidatorPluginManager()
    {
        if (!isset($this->validatorPluginManager)) {
            $this->setValidatorPluginManager(new ValidatorPluginManager());
        }
        return $this->validatorPluginManager;
    }

    public function setValidatorPluginManager(ValidatorPluginManager $manager)
    {
        $this->validatorPluginManager = $manager;
        return $this;
    }

}
