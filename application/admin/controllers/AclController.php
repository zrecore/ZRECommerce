<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Acl
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ArticleController - Article administration controller.
 *
 */
class Admin_AclController extends Zend_Controller_Action
{
	public function preDispatch() {
		// preDispach, make sure we are logged in.
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
//		if ( $zend_auth->hasIdentity() && Zre_Acl::isAllowed('acl', 'view'))
		if ( $zend_auth->hasIdentity()) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=> true));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
		$this->_helper->layout->disableLayout();
	}
	
	public function indexAction() {
		// ...Enable the jQuery javascript library
		$this->view->assign('enable_jquery', 1);
		$this->view->assign('extra_jquery_js', array('/scripts/jquery/jquery.easing.js'));
		// ...Disable cache on this page
		$this->view->assign('disable_cache', 1);
	}
	
	public function rolesAction() {
		// ...Enable the jQuery javascript library
		$this->view->assign('enable_jquery', 1);
		$this->view->assign('extra_jquery_js', array('/scripts/jquery/jquery.easing.js'));
		// ...Disable cache on this page
		$this->view->assign('disable_cache', 1);
	}
	
	public function resourcesAction() {
		// ...Enable the jQuery javascript library
		$this->view->assign('enable_jquery', 1);
		$this->view->assign('extra_jquery_js', array('/scripts/jquery/jquery.easing.js'));
		// ...Disable cache on this page
		$this->view->assign('disable_cache', 1);
	}
	
	public function allowAction() {
		// ...Enable the jQuery javascript library
		$this->view->assign('enable_jquery', 1);
		$this->view->assign('extra_jquery_js', array('/scripts/jquery/jquery.easing.js'));
		// ...Disable cache on this page
		$this->view->assign('disable_cache', 1);
	}
	
	public function denyAction() {
		// ...Enable the jQuery javascript library
		$this->view->assign('enable_jquery', 1);
		$this->view->assign('extra_jquery_js', array('/scripts/jquery/jquery.easing.js'));
		// ...Disable cache on this page
		$this->view->assign('disable_cache', 1);
	}

//	public function removeAction() {
//		$this->view->assign('disable_cache', 1);
//		
//		if($this->getRequest()->isPost()) {
//			$options['type'] = $this->getRequest()->getParam('type');
//			$options['name'] = $this->getRequest()->getParam('item');
//			$options['permtype'] = $this->getRequest()->getParam('permtype');
//			
//			$this->config->delete($options);
//		}
//	}
	
	
	
	/**
	 * Role CRUD
	 *
	 */
	
	public function ajaxRoleCreateAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxRoleReadAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxRoleUpdateAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}	
	public function ajaxRoleDeleteAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxRoleListAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	
	
	/**
	 * Resource CRUD
	 *
	 */
	
	public function ajaxResourceCreateAction() {
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
	}
	public function ajaxResourceReadAction() {
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
	}
	public function ajaxResourceUpdateAction() {
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
	}
	public function ajaxResourceDeleteAction() {
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
	}
	public function ajaxResourceListAction() {
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
	}
	
	
	/**
	 * Allow rule CRUD
	 *
	 */
	
	public function ajaxAllowCreateAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxAllowReadAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxAllowUpdateAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxAllowDeleteAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxAllowListAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	
	
	/**
	 * Deny rule CRUD
	 *
	 */
	
	public function ajaxDenyCreateAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxDenyReadAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxDenyUpdateAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxDenyDeleteAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
	public function ajaxDenyListAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$this->_helper->layout->disableLayout();
	}
}
?>