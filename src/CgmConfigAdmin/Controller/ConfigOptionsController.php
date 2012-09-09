<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CgmConfigAdmin\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

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

    public function indexAction()
    {
        $form = $this->getConfigOptionsForm();

        return array(
            'form' => $form,
        );
    }

    public function getConfigAdminService()
    {
        if (!$this->configAdminService) {
            $this->configAdminService = $this->getServiceLocator()->get('cgmconfigadmin_service');
        }
        return $this->configAdminService;
    }

    public function setConfigAdminService(ConfigAdminService $service)
    {
        $this->configAdminService = $service;
        return $this;
    }

    public function getConfigOptionsForm()
    {
        if (!$this->configOptionsForm) {
            $this->setConfigOptionsForm($this->getServiceLocator()->get('cgmconfigadmin_form'));
        }
        return $this->configOptionsForm;
    }

    public function setConfigOptionsForm(Form $form)
    {
        $this->configOptionsForm = $form;
    }
}
