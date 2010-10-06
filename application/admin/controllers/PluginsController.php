<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Plugins
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * Admin_PluginsController - Displays plugins, and provides a CRUD interface
 * to manage plugins.
 */
class Admin_PluginsController extends Zend_Controller_Action {
	
	public function preDispatch()
	{
		// preDispach, make sure we are logged in.
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() && 
			(Zre_Acl::isAllowed('plugins', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')) ) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=> true));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		
		Zre_Registry_Session::set('selectedMenuItem', 'Admin');
		Zre_Registry_Session::save();
	}
	
	public function indexAction() {
		
		$this->view->title = 'Plugins';
		
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->view->assign('disable_cache', 1);
	}
}
?>