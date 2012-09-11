<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\View\Helper;

use Zend\Form\FieldsetInterface;

class CgmConfigAdminFieldsetForm extends CgmConfigAdminAccordionForm
{
    public function renderHeader()
    {
        return '';
    }

    public function renderSectionHeader(FieldsetInterface $fieldset)
    {
        $escapeHelper    = $this->view->plugin('escapehtml');
        $translateHelper = $this->view->plugin('translate');

        $output  = '<fieldset id="' . $fieldset->getName() . '">';
        $output .= '<legend>';
        $output .= $escapeHelper($translateHelper($fieldset->getLabel()));
        $output .= '</legend>';

        return $output;
    }

    public function renderSectionFooter(FieldsetInterface $fieldset)
    {
        return '</fieldset>';
    }

    public function renderFooter()
    {
        return '';
    }
}
