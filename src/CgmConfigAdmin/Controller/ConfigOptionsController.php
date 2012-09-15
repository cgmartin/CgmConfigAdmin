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
            $result = $service->saveConfigValues($this->request->getPost());
            if (false !== $result) {
                // Success!
                // TODO: Configurable messages
                switch ($result) {
                    case ConfigAdminService::SAVE_TYPE_PREVIEW:
                        $message = '<strong>Ready to Preview</strong> ';
                        $message .= 'You may navigate the site to test your changes. ';
                        $message .= '<div><em>The changes will not be made permanent until Saved.</em></div>';
                        $message = array('message' => $message, 'type' => 'info');
                        break;
                    case ConfigAdminService::SAVE_TYPE_RESET:
                        $message = '<strong>Preview Settings have been Reset</strong> ';
                        $message = array('message' => $message);
                        break;
                    case ConfigAdminService::SAVE_TYPE_SAVE:
                    default:
                        $message = '<strong>Settings have been Saved</strong> ';
                        $messageType = 'success';
                        $message = array('message' => $message, 'type' => 'success');
                        break;
                }
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
