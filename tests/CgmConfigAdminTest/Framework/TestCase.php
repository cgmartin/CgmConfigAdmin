<?php
/**
 * CgmFeatureAdmin
 *
 * @link      http://github.com/cgmartin/CgmFeatureAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CgmConfigAdminTest\Framework;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{

    public static $locator;

    public static function setLocator($locator)
    {
        self::$locator = $locator;
    }

    public function getLocator()
    {
        return self::$locator;
    }
}
