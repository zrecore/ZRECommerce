<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage MVC
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ImagesController
 * 
 * @author
 * @version 
 */
class Admin_ImagesController extends Zend_Controller_Action 
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
		
		if ( !$zend_auth->hasIdentity() ) 
		{
			$this->_redirect('/admin/login');
		}
		
		$this->view->headScript()->appendFile('/scripts/jquery/jquery.table.sort.js');
	}
	
	public function indexAction()
	{
		$images = new Zre_Dataset_Images();
		
		
	}
}