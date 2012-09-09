<?php
/**
 * CgmFeatureAdmin
 *
 * @link      http://github.com/cgmartin/CgmFeatureAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CgmConfigAdminTest;

class SampleTest extends Framework\TestCase
{

    public function testSample()
    {
        $this->assertInstanceOf('Zend\Di\LocatorInterface', $this->getLocator());
    }
}
