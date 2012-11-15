<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\View\Helper;

use CgmConfigAdmin\Form\ConfigOptionsForm;
use Zend\View\Helper\AbstractHelper;
use Zend\InputFilter\InputFilter;
use Zend\Form\FieldsetInterface;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Radio as RadioElement;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;

class CgmConfigAdminAccordionForm extends AbstractHelper
{
    /**
     * @param \CgmConfigAdmin\Form\ConfigOptionsForm $form
     *
     * @return CgmConfigAdminAccordionForm|string
     */
    public function __invoke(ConfigOptionsForm $form = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form);
    }

    public function render(ConfigOptionsForm $form)
    {
        $formHelper    = $this->view->plugin('form');
        $elementHelper = $this->view->plugin('formelement');
        $errorsHelper  = $this->view->plugin('formelementerrors');
        $errorsHelper
            ->setMessageOpenFormat('<div class="help-block">')
            ->setMessageSeparatorString('</div><div class="help-block">')
            ->setMessageCloseString('</div>');

        $output = $this->renderHeader();

        $output .= $formHelper()->openTag($form);
        $output .= $elementHelper($form->get('csrf'));
        $output .= $errorsHelper($form->get('csrf'));

        foreach ($form->getFieldsets() as $fieldset) {

            if ($form->getNumFieldsets() > 1) {
                $output .= $this->renderSectionHeader($fieldset);
            }

            foreach ($fieldset as $element) {
                $output .= $this->renderConfigOption($element);
            }

            if ($form->getNumFieldsets() > 1) {
                $output .= $this->renderSectionFooter($fieldset);
            }
        }

        $output .= $this->renderButtons($form);
        $output .= $formHelper()->closeTag();

        $output .= $this->renderFooter();


        return $output;
    }

    public function renderHeader()
    {
        return '<div class="accordion">';
    }

    public function renderSectionHeader(FieldsetInterface $fieldset)
    {
        $escapeHelper    = $this->view->plugin('escapehtml');
        $translateHelper = $this->view->plugin('translate');

        $output  = '<div class="accordion-group" id="' . $fieldset->getName() . '">';
        $output .= '<div class="accordion-heading">';
        $output .= '<a href="#" class="accordion-toggle" data-toggle="collapse">';
        $output .= $escapeHelper($translateHelper($fieldset->getLabel()));
        $output .= '</a></div>';
        $output .= '<div class="accordion-body collapse in"><div class="accordion-inner">';

        return $output;
    }

    public function renderConfigOption(ElementInterface $element)
    {
        $labelHelper   = $this->view->plugin('formlabel');
        $elementHelper = $this->view->plugin('formelement');
        $errorsHelper  = $this->view->plugin('formelementerrors');

        $errors = $element->getMessages();
        $errorClass = (!empty($errors)) ? ' error' : '';

        $output = '<div class="control-group' . $errorClass . '">';
        $output .= $labelHelper($element->setLabelAttributes(array('class' => 'control-label')));
        $output .= '<div class="controls">';

        $labelAttributes = array();
        if ($element instanceof RadioElement) {
            $labelAttributes = array('class' => 'radio inline');
        } elseif ($element instanceof MultiCheckboxElement) {
            $labelAttributes = array('class' => 'checkbox inline');
        }
        $output .= $elementHelper($element->setLabelAttributes($labelAttributes));
        $output .= $errorsHelper($element);
        $output .= '</div></div>';

        return $output;
    }


    public function renderSectionFooter(FieldsetInterface $fieldset)
    {
        return '</div></div></div>';
    }

    public function renderFooter()
    {
        return '</div>';
    }

    public function renderButtons(ConfigOptionsForm $form)
    {
        $elementHelper   = $this->view->plugin('formelement');

        $output = '<div class="well">';
        if ($form->has('preview')) {
            $output .= $elementHelper($form->get('preview')->setAttribute('class', 'btn btn-primary btn-large'));
            $output .= ' ';
        }
        $output .= $elementHelper($form->get('save')->setAttribute('class', 'btn btn-success btn-large'));
        if ($form->has('reset')) {
            $output .= ' ';
            $output .= $elementHelper($form->get('reset')->setAttribute('class', 'btn'));
        }
        $output .= '</div>';

        return $output;
    }
}
