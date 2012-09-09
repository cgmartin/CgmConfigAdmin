<?php
/**
 * CgmFeatureAdmin
 *
 * @link      http://github.com/cgmartin/CgmFeatureAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdminTest;

class SampleTest extends Framework\TestCase
{
    public function testSample()
    {
        $this->assertInstanceOf('Zend\Di\LocatorInterface', $this->getLocator());
    }
}
