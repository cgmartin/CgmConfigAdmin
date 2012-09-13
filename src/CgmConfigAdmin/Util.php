<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin;

class Util
{
    public static function convertIdToLabel($id)
    {
        // Convert camelCase or dash-string or under_score ids to a
        // human readable label for convenience
        $label = preg_replace(
            array(
                 '/(?<=[^A-Z])([A-Z])/',
                 '/(?<=[^0-9])([0-9])/',
            ),
            ' $0',
            $id
        );
        $label = preg_replace_callback(
            '/[\-_]([a-zA-Z0-9])/',
            function ($matches) {
                return ' ' . strtoupper($matches[1]);
            },
            $label
        );
        $label = ucwords($label);
        return $label;
    }
}
