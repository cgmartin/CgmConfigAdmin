<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Form;

use ZfcBase\Form\ProvidesEventsForm;
use CgmConfigAdmin\Model\ConfigGroup;
use CgmConfigAdmin\Model\ConfigOption;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputProviderInterface;
use Zend\Form\Element\Csrf as CsrfElement;
use Zend\Form\Element\Button as ButtonElement;
use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\ValidatorPluginManager;

class ConfigOptionsForm extends ProvidesEventsForm
{
    // Maps config option types to elements
    protected static $elementMappings = array(
        'color'         => 'Zend\Form\Element\Color',
        'date'          => 'Zend\Form\Element\Date',
        'datetimelocal' => 'Zend\Form\Element\DateTimeLocal',
        'email'         => 'Zend\Form\Element\Email',
        'month'         => 'Zend\Form\Element\Month',
        'multicheckbox' => 'Zend\Form\Element\MultiCheckbox',
        'number'        => 'Zend\Form\Element\Number',
        'password'      => 'Zend\Form\Element\Password',
        'radio'         => 'Zend\Form\Element\Radio',
        'range'         => 'Zend\Form\Element\Range',
        'select'        => 'Zend\Form\Element\Select',
        'text'          => 'Zend\Form\Element\Text',
        'textarea'      => 'Zend\Form\Element\Textarea',
        'time'          => 'Zend\Form\Element\Time',
        'url'           => 'Zend\Form\Element\Url',
        'week'          => 'Zend\Form\Element\Week',
    );

    /**
     * @var ValidatorPluginManager
     */
    protected $validatorPluginManager;

    /**
     * @param  array            $groups  Optional array of ConfigGroups
     * @param  null|int|string  $name    Optional name for the form
     * @param  array            $options Optional array of options
     */
    public function __construct(array $groups = array(), $name = null, array $options = array())
    {
        $isPreviewEnabled = true;
        if (isset($options['preview_enabled'])) {
            $isPreviewEnabled = $options['preview_enabled'];
            unset($options['preview_enabled']);
        }

        parent::__construct($name, $options);
        $this->filter = new InputFilter();

        $this->setAttribute('class', 'form-horizontal');

        if (!empty($groups)) {
            $this->addConfigGroups($groups);
        }

        $this->addActionButtons($isPreviewEnabled);

        $csrf = new CsrfElement('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => null));
        $this->add($csrf);
    }

    /**
     * @param  boolean $enabled
     * @return ConfigOptionsForm
     */
    public function setIsPreviewEnabled($enabled)
    {
        return $this->addActionButtons($enabled);
    }

    /**
     * @param  boolean $isPreviewEnabled
     * @return ConfigOptionsForm
     */
    public function addActionButtons($isPreviewEnabled = true)
    {
        $this->remove('reset');
        $this->remove('preview');
        $this->remove('save');

        if ($isPreviewEnabled) {
            $resetBtn = new ButtonElement('reset');
            $resetBtn
                ->setLabel('Reset')
                ->setAttribute('type', 'submit')
                ->setValue('1');
            $this->add($resetBtn);

            $previewBtn = new ButtonElement('preview');
            $previewBtn
                ->setLabel('Preview')
                ->setAttribute('type', 'submit')
                ->setValue('1');
            $this->add($previewBtn);
        }

        $saveBtn = new ButtonElement('save');
        $saveBtn
            ->setLabel('Save')
            ->setAttribute('type', 'submit')
            ->setValue('1');
        $this->add($saveBtn);

        return $this;
    }

    /**
     * @param  array $groups
     * @return ConfigOptionsForm
     */
    public function addConfigGroups(array $groups)
    {
        // Add fieldsets for all defined groups
        usort($groups, function ($a, $b) {
            return strnatcasecmp($a->getSort(), $b->getSort());
        });
        foreach ($groups as $groupId => $configGroup) {
            $this->add($this->createConfigGroupElementSpec($configGroup));
            $this->filter->add(
                $this->createConfigGroupInputFilterSpec($configGroup),
                $configGroup->getId()
            );
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getNumFieldsets()
    {
        return count($this->fieldsets);
    }

    /**
     * Create a form element spec from a ConfigGroup
     *
     * @return array
     */
    protected function createConfigGroupElementSpec(ConfigGroup $configGroup)
    {
        $elementSpec = array();

        $elementSpec['type'] = 'Zend\Form\Fieldset';
        $elementSpec['name'] = $configGroup->getId();
        $elementSpec['options']['label'] = $configGroup->getLabel();

        foreach ($configGroup->getConfigOptions() as $id => $configOption) {
            $elementSpec['elements'][]['spec'] = $this->createConfigOptionElementSpec($configOption);
        }

        return $elementSpec;
    }

    /**
     * Create a form element spec from a ConfigOption
     *
     * @return array
     */
    protected function createConfigOptionElementSpec(ConfigOption $configOption)
    {
        $configOption->prepare();
        $elementSpec = array();

        $type = $configOption->getInputType();
        $elementSpec['type'] = (array_key_exists($type, self::$elementMappings))
            ? self::$elementMappings[$type] : $type;

        $elementSpec['name']             = $configOption->getId();
        $elementSpec['options']['label'] = $configOption->getLabel();

        // Value
        if (null !== ($value = $configOption->getValue())) {
            $elementSpec['attributes']['value'] = $value;
        }

        // Value Options
        if (null !== ($valueOptions = $configOption->getValueOptions())) {
            $elementSpec['options']['value_options'] = $valueOptions;
        }

        return $elementSpec;
    }

    /**
     * Create an input filter spec from a ConfigGroup
     *
     * @param  ConfigGroup $configGroup
     * @return array
     */
    protected function createConfigGroupInputFilterSpec(ConfigGroup $configGroup)
    {
        $inputFilters = array(
            'type' => 'Zend\InputFilter\InputFilter',
        );

        foreach ($configGroup->getConfigOptions() as $id => $configOption) {
            $inputFilters[$id] = $this->createConfigOptionInputFilterSpec(
                $configOption, $configGroup->getId()
            );
        }
        return $inputFilters;
    }

    /**
     * Create an input filter spec from a ConfigOption
     *
     * @param  ConfigOption $configOption
     * @return array
     */
    protected function createConfigOptionInputFilterSpec(ConfigOption $configOption, $groupId)
    {
        $element = $this->get($groupId)->get($configOption->getId());
        if ($element instanceof InputProviderInterface) {
            $inputSpec = $element->getInputSpecification();
        } else {
            $inputSpec = array();
            $inputSpec['name'] = $configOption->getId();
        }

        $inputSpec['required']    = $configOption->getRequired();
        $inputSpec['allow_empty'] = ! $configOption->getRequired();

        return $inputSpec;
    }

    /**
     * @return ValidatorPluginManager
     */
    public function getValidatorPluginManager()
    {
        if (!isset($this->validatorPluginManager)) {
            $this->setValidatorPluginManager(new ValidatorPluginManager());
        }
        return $this->validatorPluginManager;
    }

    /**
     * @param  ValidatorPluginManager $manager
     * @return ConfigOptionsForm
     */
    public function setValidatorPluginManager(ValidatorPluginManager $manager)
    {
        $this->validatorPluginManager = $manager;
        return $this;
    }

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
}
