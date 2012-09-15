<?php
/**
 * CgmConfigAdmin
 *
 * @link      http://github.com/cgmartin/CgmConfigAdmin for the canonical source repository
 * @copyright Copyright (c) 2012 Christopher Martin (http://cgmartin.com)
 * @license   New BSD License https://raw.github.com/cgmartin/CgmConfigAdmin/master/LICENSE
 */

namespace CgmConfigAdmin\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class CgmFlashMessages extends AbstractHelper
{
    /**
     * @var FlashMessenger
     */
    protected $flashMessenger;

    public function __construct(FlashMessenger $plugin)
    {
        $this->flashMessenger = $plugin;
    }

    public function __invoke()
    {
        $output = '';
        $this->flashMessenger->setNamespace('cgmconfigadmin');
        if (count($this->flashMessenger)) {
            foreach ($this->flashMessenger as $msg) {
                $class = (isset($msg['type'])) ? "alert-{$msg['type']}" : '';
                $output .= '<div class="alert '. $class .'">';
                $output .= '<button type="button" class="close" data-dismiss="alert">×</button>';
                $output .= $msg['message'];
                $output .= '</div>';
            }
        }
        $this->flashMessenger->setNamespace('default');
        return $output;
    }
}
