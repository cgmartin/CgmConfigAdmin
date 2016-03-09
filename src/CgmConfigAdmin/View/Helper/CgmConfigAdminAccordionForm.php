<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\View\Helper;

use CgmConfigAdmin\Form\ConfigOptionsForm;
use Zend\View\Helper\AbstractHelper;
use Zend\Form\FieldsetInterface;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Radio as RadioElement;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;

class CgmConfigAdminAccordionForm extends AbstractHelper
{
    /**
     * @param  ConfigOptionsForm $form
     * @return CgmConfigAdminAccordionForm|string
     */
    public function __invoke(ConfigOptionsForm $form = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form);
    }

    /**
     * @param  ConfigOptionsForm $form
     * @return string
     */
    public function render(ConfigOptionsForm $form)
    {
        $formHelper    = $this->view->plugin('form');
        $elementHelper = $this->view->plugin('formelement');
        $errorsHelper  = $this->view->plugin('formelementerrors');
        $errorsHelper
            ->setMessageOpenFormat('<div class="help-block">')
            ->setMessageSeparatorString('</div><div class="help-block">')
            ->setMessageCloseString('</div>');

        $output  = $formHelper()->openTag($form);
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

        return $output;
    }

    /**
     * @param  FieldsetInterface $fieldset
     * @return string
     */
    public function renderSectionHeader(FieldsetInterface $fieldset)
    {
        $escapeHelper    = $this->view->plugin('escapehtml');
        $translateHelper = $this->view->plugin('translate');
        $id =  $fieldset->getName();

        $output  = '<div class="panel panel-default">';
        $output .= '<div class="panel-heading" role="tab" id="heading'.$id.'">';
        $output .= '<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$id.'" aria-expanded="true">';
        $output .= $escapeHelper($translateHelper($fieldset->getLabel()));
        $output .= '</a></h4></div>';
        $output .= '<div id="collapse'.$id.'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading'.$id.'">';
        $output .= '<div class="panel-body"><div class="col-md-12">';

        return $output;
    }

    /**
     * @param  ElementInterface $element
     * @return string
     */
    public function renderConfigOption(ElementInterface $element)
    {
        $labelHelper   = $this->view->plugin('formlabel');
        $elementHelper = $this->view->plugin('formelement');
        $errorsHelper  = $this->view->plugin('formelementerrors');

        $errors = $element->getMessages();
        $errorClass = (!empty($errors)) ? ' error' : '';

        $output = '<div class="form-group' . $errorClass . '">';
        $output .= $labelHelper($element);

        $element->setAttributes(array('class' => 'form-control'));

        $labelAttributes = array();
        if ($element instanceof RadioElement) {
            $labelAttributes = array('class' => 'radio inline');
        } elseif ($element instanceof MultiCheckboxElement) {
            $labelAttributes = array('class' => 'checkbox inline');
        }
        $output .= $elementHelper($element->setLabelAttributes($labelAttributes));
        $output .= $errorsHelper($element);
        $output .= '</div>';

        return $output;
    }

    /**
     * @param  FieldsetInterface $fieldset
     * @return string
     */
    public function renderSectionFooter(FieldsetInterface $fieldset)
    {
        return '</div></div></div></div>';
    }

    /**
     * @param  ConfigOptionsForm $form
     * @return string
     */
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
