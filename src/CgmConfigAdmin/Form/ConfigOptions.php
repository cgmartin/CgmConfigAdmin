<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CgmConfigAdmin\Form;

use ZfcBase\Form\ProvidesEventsForm;
use CgmConfigAdmin\Options\ModuleOptions;

class ConfigOptions extends ProvidesEventsForm
{
    /**
     * @param  ModuleOptions    $options options for the element
     * @param  null|int|string  $name    Optional name for the element
     */
    public function __construct(array $options, $name = null)
    {
        parent::__construct($name);

        var_export($options);
    }
}