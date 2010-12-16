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
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Settings');

		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
	}

	public function checkoutAction() {
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Checkout');

		// ...Get list of adapters
		$dir = BASE_PATH . '/library/Checkout/Adapter/';
		$files = Zre_File::ls($dir);
		
		$adapters = array();
		foreach($files as $file) {
			
			if (file_exists($dir . $file . '.php')) {
				$info = pathinfo($dir . $file . '.php');
				
				if ($info['extension'] == 'php' && $info['filename'] != 'Interface') {
					$adapters[] = $info['filename'];
				}
				unset($info);
			}
		}

		$this->view->adapters = $adapters;

		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
	}

	public function overviewAction()
	{
		// ..Grab our statistical and status data
		$settings = Zre_Config::getSettingsCached();
		$pre = $settings->db->table_name_prepend;

		$articles = new Zre_Dataset_Article();
		$products = new Zre_Dataset_Product();
		$orders = new Zre_Dataset_Orders();

		/**
		 * Article count
		 */
		// ...Published article count.
		$publishedArticleCount = $articles->listAll(
			array(
				'published' => 'yes'
			),
			array(
				'from' => array(
					'name' => array('a' => $pre . 'article'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->publishedArticleCount = $publishedArticleCount;

		// ...Non-published article count.
		$notPublishedArticleCount = $articles->listAll(
			array(
				'published' => 'no'
			),
			array(
				'from' => array(
					'name' => array('a' => $pre . 'article'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->notPublishedArticleCount = $notPublishedArticleCount;

		// ...Archived article count.
		$archivedArticleCount = $articles->listAll(
			array(
				'published' => 'archived'
			),
			array(
				'from' => array(
					'name' => array('a' => $pre . 'article'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->archivedArticleCount = $archivedArticleCount;

		/**
		 * Product count
		 */
		// ...Published product count.
		$productCount = $products->listAll(
			array(
				'published' => 'yes'
			),
			array(
				'from' => array(
					'name' => array('p' => $pre . 'product'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();


		$this->view->productCount = $productCount;

		/**
		 * Order count
		 */
		$pendingOrderCount = $orders->listAll(
			array(
				'status' => 'pending'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->pendingOrderCount = $pendingOrderCount;

		$shippedOrderCount = $orders->listAll(
			array(
				'status' => 'shipped'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->shippedOrderCount = $shippedOrderCount;

		$voidOrderCount = $orders->listAll(
			array(
				'status' => 'void'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->voidOrderCount = $voidOrderCount;

		$exchangedOrderCount = $orders->listAll(
			array(
				'status' => 'exchanged'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->exchangedOrderCount = $exchangedOrderCount;

		$refundedOrderCount = $orders->listAll(
			array(
				'status' => 'refunded'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->refundedOrderCount = $refundedOrderCount;

		$awaitingReturnOrderCount = $orders->listAll(
			array(
				'status' => 'awaiting_return'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->awaitingReturnOrderCount = $awaitingReturnOrderCount;

		$completeOrderCount = $orders->listAll(
			array(
				'status' => 'complete'
			),
			array(
				'from' => array(
					'name' => array('o' => $pre . 'orders'),
					'cols' => array('total' => new Zend_Db_Expr('COUNT(*)'))
				)
			),
			false
		)->current();

		$this->view->completeOrderCount = $completeOrderCount;
	}
	
	public function configAction()
	{
		
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Configuration');
		$request = $this->getRequest();
		
		//@todo - This should probably be a define or something instead of hardcoded in two places
		$settingsPath = realpath(APPLICATION_PATH . '/settings/environment/settings.xml');;
//		$object = simplexml_load_file($settingsPath);
//
//		$formSettingsClass = new Zre_Ui_Form_Settings($object, true);
//		$form = $formSettingsClass->getFormObject();
//		$formValues = $this->getRequest()->getParams();

		$xmlString = Zre_File::read($settingsPath);
		$this->view->xml_string = $xmlString;
		
//		if (isset($xmlString)) {
//
//		    if($form->isValid($formValues)) {
//
//			unset($formValues['Submit']);
//			unset($formValues['is_submitted']);
//			unset($formValues['action']);
//			unset($formValues['controller']);
//			unset($formValues['module']);
//
//			$config = new Zend_Config($formValues, true);
//			$config->setExtend('dev', 'production');
//
//			Zre_Config::saveSettings($config, $settingsPath);
//			Zre_Config::flush();
//			Zre_Config::loadSettings($settingsPath, false);
//
//			$this->view->assign('content', '<div class="ok">'.$t->_('Ok:').' '. $t->_('Your new settings have been saved.') .'</div>'. str_pad('', 48, '<br />'));
//			$form = null;
//
//
//		    }
//		    else {
//			//Do nothing right now
//		    }
//		}
//
//		$this->view->form = $form;
		
		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();
		
	}
	
	public function configAjaxAction() 
	{
		try {
			$request = $this->getRequest();
			$reply = null;

			$settingsPath = realpath(APPLICATION_PATH . '/settings/environment/settings.xml');

			$xmlString = $request->getParam('xml', null);

			if( isset($xmlString) ) {
				$config = new Zend_Config_Xml($xmlString);
				$config->setExtend('dev', 'production');

				Zre_Config::saveSettings($config, $settingsPath);
				Zre_Config::flush();
				Zre_Config::loadSettings($settingsPath, false);
				$reply = array(
					'result' => 'ok',
					'desc' => 'Settings saved.'
				);
			}
		} catch (Exception $e) {
			$reply = array(
				'result' => 'error',
				'desc' => (string) $e
			);
		}
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
	
	public function phpEnvironmentAction() {
		
	}

}