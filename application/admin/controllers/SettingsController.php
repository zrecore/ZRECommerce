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
class Admin_SettingsController extends Zend_Controller_Action
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
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Settings');
		
		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
	}
	
	public function configAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Configuration');
		
		//@todo - This should probably be a define or something instead of hardcoded in two places
		$settingsPath = realpath('../application/settings/environment') . DIRECTORY_SEPARATOR . 'settings.xml';
		$object = simplexml_load_file($settingsPath);
			
		$formSettingsClass = new Zre_Ui_Form_Settings($object, true);
		$form = $formSettingsClass->getFormObject();
		$formValues = $this->getRequest()->getParams();
		
		if ($this->getRequest()->getParam('is_submitted') == 1) {
			
		    if($form->isValid($formValues)) {
			
			unset($formValues['Submit']);
			unset($formValues['is_submitted']);
			unset($formValues['action']);
			unset($formValues['controller']);
			unset($formValues['module']);
			
			$config = new Zend_Config($formValues, true);
			$config->setExtend('dev', 'production');
					
			Zre_Config::saveSettings($config, $settingsPath);
			Zre_Config::flush();
			Zre_Config::loadSettings($settingsPath, false);
			
			$this->view->assign('content', '<div class="ok">'.$t->_('Ok:').' '. $t->_('Your new settings have been saved.') .'</div>'. str_pad('', 48, '<br />'));
			$form = null;
			
			
		    }
		    else {
			//Do nothing right now
		    }
		}
		
		$this->view->form = $form;
		
		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
	}
	
	public function configAjaxAction() 
	{
		$request = $this->getRequest();
		$reply = null;
		
		$settingsPath = realpath('../application/settings/environment') . DIRECTORY_SEPARATOR . 'settings.xml';
		$object = simplexml_load_file($settingsPath);
		
		$formValues = $request->getParams();
		$formSettingsClass = new Zre_Ui_Form_Settings($object, true);
		$form = $formSettingsClass->getFormObject();
		
		unset($formValues['Submit']);
		unset($formValues['is_submitted']);
		unset($formValues['action']);
		unset($formValues['controller']);
		unset($formValues['module']);
		
//		if($form->isValid($formValues)) {
					
			$config = new Zend_Config($formValues, true);
			$config->setExtend('dev', 'production');
					
			Zre_Config::saveSettings($config, $settingsPath);
			Zre_Config::flush();
			Zre_Config::loadSettings($settingsPath, false);
			$reply = array(
				'result' => 'ok',
				'desc' => 'Settings saved.'
			);
//		} else {
//			//Do nothing right now
//			
//			$reply = array(
//				'result' => 'error',
//				'desc' => 'Invalid parameters. ',
//				'debug' => print_r($formValues, true)
//			);
//	    }
//	    
	    $this->_helper->json($reply, true);
	}
	
	public function importAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Import/Export');
		
		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
	}
	
	public function errorsAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Errors');
		
		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
	}
	
	public function updatesAction() {
		
	}

}