<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Index
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Admin_IndexController - Default administration controller.
 *
 */
class Admin_IndexController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/');
		}
		
		// All pages here require a valid login. Kick out if invalid.
		
		if ( $zend_auth->hasIdentity() ) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login');
		}
		
		$this->view->headScript()->appendFile('/scripts/jquery/jquery.table.sort.js');
		$this->view->headScript()->appendFile('/scripts/jquery/jquery.imagelist.js');
		$this->view->headLink()->appendStylesheet('/scripts/jquery/imagelist/default-theme.css');
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		/**
		 * This should be the "dashboard" landing page.
		 * 
		 * Other administrative controllers can be linked to from here.
		 */
		$this->view->headScript()->appendFile('/scripts/jquery/wymeditor/jquery.wymeditor.min.js');
		$this->view->headScript()->appendFile('/scripts/jquery/jquery.simpletree.js');
		$settings = Zre_Config::getSettingsCached();

    	$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Admin');
		
		Zre_Registry_Session::set('selectedMenuItem', 'Admin');
		Zre_Registry_Session::save();
	}
}