<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Login
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce All rights reserved.
 * @license GPL v3 or higher. See README file.
 */

/**
 * LoginController - Sign in and sign off controller.
 * 
 */
class Admin_LoginController extends Zend_Controller_Action {

	public function preDispatch()
	{
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session());
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		if ($zend_auth->hasIdentity()) {
			switch($this->getRequest()->getActionName()) {
				case 'logout':
					// Allow logout.
					break;
				case 'index': 	// ...Break statement purposely omitted.
				default:		// ...Already logged in, redirect to admin page.
					if ($zend_auth->hasIdentity()) $this->_redirect('/admin/', array('exit', true));
			}
		}
		
		Zre_Registry_Session::set('selectedMenuItem', 'Sign In');
		Zre_Registry_Session::save();
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated LoginController::indexAction() default action
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session());
		
		
		// ...Ditch dojo, we don't currently use a 'minified' version.
//		$this->view->assign('enable_dojo', true); // We want client-side validation, too. (w/ Dojo)
		$this->view->assign('disable_cache', 1); // We want dynamic messages, if form is invalid. 
		
		$settings = Zre_Config::getSettingsCached();
    	
		$form = new Zre_Ui_Form_Login();
		$form->setAction('/admin/login');
		$t = Zend_Registry::get('Zend_Translate');
		
		$this->view->title = $t->_('Login');
		
		$this->view->assign('content', "<h3 class=\"content_title\">{$t->_('Login')}</h3>".$t->_('Please, log in to continue.').'<br /><br />');
		
		/**
		 * Validation
		 */
		$is_sumbitted = $this->getRequest()->getParam('is_submitted');
		if ($is_sumbitted)
		{
			if ($form->isValid($this->getRequest()->getParams()) )
			{
				// Form seems valid, validate credentials.
				/**
				 * @todo You can specify PDO mysql settings to connect to different databases.
				 */
				$db = Zre_Db_Mysql::getInstance();
				
				$is_valid = false;
				
				$username = $this->getRequest()->getParam('name');
				$pw = $this->getRequest()->getParam('password');
				
				$auth_adapter = new Zend_Auth_Adapter_DbTable(
				$db,
				((string)$settings->db->table_name_prepend) . 'users',
					"name",
					"password"
				);
																
				$auth_adapter->setIdentity($username);
				$auth_adapter->setCredential( md5($pw) );
				
				$zend_result = $zend_auth->authenticate($auth_adapter);
				$is_valid = $zend_result->isValid();
				
				if ($is_valid == true)
				{
					$form->resetLoginAttemptCount();
//					Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
					
					$userAuth = (array) $auth_adapter->getResultRowObject(null, array('password'));
					
					Zre_Registry_Session::set('CURRENT_USER', $userAuth);
					Zre_Registry_Session::set('CURRENT_USER_PROFILE', Zre_Dataset_Users::getUserProfile( $userAuth['user_id'] ));
					Zre_Registry_Session::save();
					
					$this->_redirect('/admin/');
				} else {
					$this->view->assign('content', "<h3 class=\"content_title\">{$t->_('Login')}</h3>\n<br />".'<b class="errors">'.$t->_('Invalid user name. Please try a different one.').'</b><br /><br />');
					$form->deductLoginAttemptCount();
				}
			} else {
				// Invalid form fields.
				$form->deductLoginAttemptCount();
			}
			
		}
		
		$login_count = $form->getLoginAttemptCount();
		if ($login_count > 0 && $login_count < $form->max_login_attempts )
		{
			echo str_replace('%value%', $login_count, $t->_("(You have %value% attempts left.)") );
		}
		$this->view->form = $form;
	}
	
	public function logoutAction()
	{
		$zend_auth = Zend_Auth::getInstance();
		if ($zend_auth->hasIdentity())
		{
			$zend_auth->clearIdentity();
			Zre_Registry_Session::flush();
			
			Zend_Session::forgetMe();
			Zend_Session::regenerateId();
		}
		$this->_redirect('/', array('exit'=>true));
	}
	
	public function registerAction()
	{
		$t = Zend_Registry::get('Zend_Translate');
		
		$this->view->title = $t->_('Register');
		
		//If registration is disabled, just display registration disabled message
        if(strtolower(Zre_Config::getSettingsCached()->site->registration_enabled) != 'yes') {
            $this->view->assign('content', $t->_('User registration has been disabled on this site.'));
            $form = null;
            return null;
        }
		
		$this->view->assign('enable_dojo', true); // We want client-side validation, too. (w/ Dojo)
		$this->view->assign('disable_cache', 1); // We want dynamic messages, if form is invalid.
		
		$form = new Zre_Ui_Form_Register( null, $this->getRequest());
		
		$is_sumbitted = $this->getRequest()->getParam('is_submitted');
		$this->view->assign('content', 
			$t->_("Fill out this form to register.") .'<br /><br />
			<div class="required">&nbsp;&bull;'.$t->_('Required').'</div>
			<div class="not_required">&nbsp;&bull;'.$t->_('Optional').'</div><br /><br />' );
			
		if ($is_sumbitted)
		{
			if ($form->isValid($this->getRequest()->getParams()) )
			{
				// Form seems valid, process!
				$results = Zre_Auth_User::addUser($this->getRequest()->getParams());
				if ( $results === true )
				{
					$this->view->assign('content', '<div class="ok">'.$t->_('Ok:').' '. sprintf($t->_('Welcome, %s'), $this->getRequest()->getParam('name')) .'</div>'. str_pad('', 48, '<br />'));
					$form = null;

				} else {
					
//					echo "[FAIL]"; 
					/**
					 * @todo Use the correct field such as 
					 */
//					if (substr($results->getMessage(), 0, strlen('SQLSTATE[23000]:')) == 'SQLSTATE[23000]:' )
//					{
					if ( substr_count($results->getMessage(),'1062 Duplicate entry' ) > 0 )
					{
						$this->view->assign('content', '<div class="errors">'.$t->_('Error:').' '.$t->_('Invalid user name. Please try a different one.').'</div>');
					} else {
						$this->view->assign('content', '<div class="errors">'.$t->_('Error:').' '.$t->_('Could not register new user. Please try again.').'</div>');
					}
				}
			} else {
				// Invalid form fields. Allow form to render.
			}
			
		}
		
			
		$this->view->form = $form;
	}
	
	public function forgotAction()
	{
		
	}
}