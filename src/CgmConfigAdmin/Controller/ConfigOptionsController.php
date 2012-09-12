<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use CgmConfigAdmin\Service\ConfigAdmin as ConfigAdminService;

class ConfigOptionsController extends AbstractActionController
{
    /**
     * @var ConfigAdminService
     */
    protected $configAdminService;

    /**
     * @var Form
     */
    protected $configOptionsForm;

    /**
     * Index Action
     *
     * Process the Config Options form
     *
     * @return array
     */
    public function indexAction()
    {
        $service = $this->getConfigAdminService();

        if ($this->request->isPost()) {
            if ($service->saveConfigValues($this->request->getPost())) {
                // Success!
            }
        }
        return array(
            'form' => $service->getConfigOptionsForm(),
        );
    }

    /**
     * @return ConfigAdminService
     */
    public function getConfigAdminService()
    {
        if (!$this->configAdminService) {
            $this->configAdminService = $this->getServiceLocator()->get('cgmconfigadmin_service');
        }
        return $this->configAdminService;
    }

    /**
     * @param  ConfigAdminService $service
     * @return ConfigOptionsController
     */
    public function setConfigAdminService(ConfigAdminService $service)
    {
        $this->configAdminService = $service;
        return $this;
    }


}
