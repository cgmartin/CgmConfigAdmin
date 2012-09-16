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
            $config = $this->request->getPost();
            $successful = false;
            if (!empty($config['preview'])) {
                if ($service->previewConfigValues($config)) {
                    $message = '<strong>Ready to preview</strong> ';
                    $message .= 'You may navigate the site to test your changes. ';
                    $message .= '<div><em>The changes will not be made permanent until saved.</em></div>';
                    $message = array('message' => $message, 'type' => 'info');
                    $successful = true;
                }

            } else if (!empty($config['reset'])) {
                $service->resetConfigValues();
                $message = '<strong>Preview Settings have been reset</strong> ';
                $message = array('message' => $message);
                $successful = true;

            } else if (!empty($config['save'])) {
                if ($service->saveConfigValues($config)) {
                    $message = '<strong>Settings have been saved</strong> ';
                    $message = array('message' => $message, 'type' => 'success');
                    $successful = true;
                }
            }

            if ($successful) {
                $this->flashMessenger()
                    ->setNamespace('cgmconfigadmin')
                    ->addMessage($message);
                return $this->redirect()->toRoute('cgmconfigadmin');
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
            $this->configAdminService = $this->getServiceLocator()->get('cgmconfigadmin');
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
