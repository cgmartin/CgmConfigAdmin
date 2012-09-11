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

        $fieldsets = array();

        // Add default fieldset for elements without a group
        $defaultFieldset = new Fieldset('default');
        $defaultFieldset->setLabel('Settings');
        $fieldsets['default'] = $defaultFieldset;

        // Add fieldsets for all defined groups
        foreach ($options->getConfigGroups() as $groupKey => $groupLabel) {
            $fieldset = new Fieldset($groupKey);
            $fieldset->setLabel($groupLabel);
            $fieldsets[$groupKey] = $fieldset;
        }

        // Add Elements to fieldsets
        foreach ($options->getConfigOptions() as $configOption) {
            $group = $configOption->getGroup();
            if (!array_key_exists($group, $fieldsets)) {
                throw new \UnexpectedValueException(
                    sprintf('Undefined Config Option group (%s)', $group)
                );
            }
            $fieldsets[$group]->add($configOption->createFormElementSpec());
            $ele = $fieldsets[$group]->get($configOption->getId());
        }

        // Add fieldsets to form
        foreach ($fieldsets as $key => $fieldset) {
            if ($fieldset->count() > 0) {
                $this->add($fieldset);
                $this->numFieldsets++;
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
    }

    /**
     * @return int
     */
    public function getNumFieldsets()
    {
        return $this->numFieldsets;
    }
}