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