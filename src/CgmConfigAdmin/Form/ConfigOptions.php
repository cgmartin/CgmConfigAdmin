<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Form;

use ZfcBase\Form\ProvidesEventsForm;
use CgmConfigAdmin\Options\ModuleOptions;
use Zend\InputFilter\InputFilter;
use Zend\Form\Fieldset;
use Zend\Form\Element\Csrf as CsrfElement;
use Zend\Form\Element\Button as ButtonElement;

class ConfigOptions extends ProvidesEventsForm
{
    /**
     * @var int
     */
    protected $numFieldsets = 0;

    /**
     * @param  ModuleOptions    $options options for the form
     * @param  null|int|string  $name    Optional name for the form
     */
    public function __construct(ModuleOptions $options, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('class', 'form-horizontal');

        $formInputFilter = new InputFilter();
        $fieldsets       = array();
        $inputFilters    = array();

        // Add default fieldset for elements without a group
        $defaultFieldset = new Fieldset('default');
        $defaultFieldset->setLabel('Settings');
        $fieldsets['default'] = $defaultFieldset;

        // Add fieldsets for all defined groups
        foreach ($options->getConfigGroups() as $groupKey => $groupLabel) {
            $fieldset = new Fieldset($groupKey);
            $fieldset->setLabel($groupLabel);
            $fieldsets[$groupKey] = $fieldset;
            $inputFilters[$groupKey] = array(
                'type' => 'Zend\InputFilter\InputFilter',
            );
        }

        // Add Elements to fieldsets
        foreach ($options->getConfigOptions() as $configOption) {
            $configOption->prepareForForm();
            $group = $configOption->getGroup();
            if (!array_key_exists($group, $fieldsets)) {
                throw new \UnexpectedValueException(
                    sprintf('Undefined Config Option group (%s)', $group)
                );
            }
            $fieldsets[$group]->add($configOption->createFormElementSpec());
            $inputFilters[$group][$configOption->getId()] = $configOption->createInputFilterSpec();
        }

        // Add fieldsets and inputfilters to form
        foreach ($fieldsets as $key => $fieldset) {
            if ($fieldset->count() > 0) {
                $this->add($fieldset);
                $this->numFieldsets++;
                $formInputFilter->add($inputFilters[$key], $key);
            }
        }

        $csrf = new CsrfElement('csrf');
        $csrf->setCsrfValidatorOptions(array('timeout' => null));
        $this->add($csrf);

        $resetBtn = new ButtonElement('resetBtn');
        $resetBtn
            ->setLabel('Reset')
            ->setAttribute('type', 'submit');
        $this->add($resetBtn);

        $saveBtn = new ButtonElement('saveBtn');
        $saveBtn
            ->setLabel('Save')
            ->setAttribute('type', 'submit');
        $this->add($saveBtn);

        $previewBtn = new ButtonElement('previewBtn');
        $previewBtn
            ->setLabel('Preview')
            ->setAttribute('type', 'submit');
        $this->add($previewBtn);

        $this->setInputFilter($formInputFilter);
    }

    /**
     * @return int
     */
    public function getNumFieldsets()
    {
        return $this->numFieldsets;
    }
}