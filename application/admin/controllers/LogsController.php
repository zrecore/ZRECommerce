<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Logs
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * LogController - Log view and log settings administration controller.
 *
 */
class Admin_LogsController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() 
			&& 
			(Zre_Acl::isAllowed('logs', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL'))
		) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=>'true'));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$this->view->title = 'Errors';
		
		Zre_Registry_Session::set('selectedMenuItem', 'Errors');
		Zre_Registry_Session::save();
		
	}

}