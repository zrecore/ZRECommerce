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

        public function checkoutAction() {
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Checkout');
		$settings = Zre_Config::getSettingsCached();

		$adapter = (string)$settings->merchant->adapter;
		$this->view->selectedAdapter = $adapter;

		// ...Get list of adapters
		$dir = BASE_PATH . '/library/Checkout/Adapter/';
		$files = Zre_File::ls($dir);

		$adapters = array();
		foreach($files as $file) {
			if (file_exists($dir . $file) && !is_dir($dir . $file)) {
				$info = pathinfo($dir . $file);
				
				if ($info['extension'] == 'php' && $info['filename'] != 'Interface') {
					$adapters[] = basename($info['filename']);
				}
				unset($info);
			}
		}

		$this->view->adapters = $adapters;

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

        public function configCheckoutAjaxAction() {
		try {
			$request = $this->getRequest();
			$reply = null;

			$settingsPath = realpath(APPLICATION_PATH . '/settings/environment/settings.xml');

			$com = $request->getParam('com', null);

			switch ($com) {
				case 'set_adapter':
					$selectedAdapter = $request->getParam('adapter');
					$xml = Zre_File::read($settingsPath);

					$config = new Zend_Config_Xml($xml, null, true);
					$config = Zre_Config::parseSettings($config, $config->runmode);

					$runMode = $config->runmode->use;

					$config->{$runMode}->merchant->adapter = $selectedAdapter;

					Zre_Config::saveSettings($config, $settingsPath);
					Zre_Config::loadSettings($settingsPath, true);
					$reply = array(
						'result' => 'ok',
						'desc' => 1
					);

					break;
				default:
					$reply = array(
						'result' => 'error',
						'desc' => 'No command specified.'
					);
					break;
			}

		} catch (Exception $e) {
			$reply = array(
				'result' => 'error',
				'desc' => (string) $e
			);
		}

		$this->_helper->json($reply, true);
	}

        public function configRunmodeAjaxAction() {
            $request = $this->getRequest();

            try {
                $com = $request->getParam('com', null);

                switch ($com) {
                    case 'set_runmode':
                        $newRunmode = $request->getParam('runmode', null);

                        if (isset($newRunmode)) {
                            $settingsPath = APPLICATION_PATH . '/settings/environment/settings.xml';
                            $xmlString = Zre_File::read($settingsPath);
                            $config = new Zend_Config_Xml($xmlString, null, true);
                            $config = Zre_Config::parseSettings($config, $config->runmode);

                            $config->runmode->use = $newRunmode;

                            Zre_Config::saveSettings($config, $settingsPath);
                            Zre_Config::loadSettings($settingsPath, true);

                            $reply = array(
                                    'result' => 'ok',
                                    'desc' => 1
                            );
                        } else {
                            throw new Exception('No value specified.');
                        }
                        break;
                    default:
                        $reply = array(
                            'result' => 'error',
                            'desc' => 'No command specified'
                        );
                        break;
                }
            } catch (Exception $e) {
                $reply = array(
                    'result' => 'error',
                    'desc' => (string) $e
                );
            }

            $this->_helper->json($reply, true);
        }
        public function configSecurityKeysAjaxAction() {
		$request = $this->getRequest();

		try {
			$com = $request->getParam('com', null);

			switch ($com) {
				case 'set_salt';
					$newSalt = $request->getParam('salt', null);
					if (isset($newSalt)) {
						$settingsPath = APPLICATION_PATH . '/settings/environment/settings.xml';
						$xmlString = Zre_File::read($settingsPath);
						$config = new Zend_Config_Xml($xmlString, null, true);
                                                $config = Zre_Config::parseSettings($config, $config->runmode);

                                                $runMode = $config->runmode->use;

						$config->{$runMode}->site->cryptographicSalt = $newSalt;

						Zre_Config::saveSettings($config, $settingsPath);
						Zre_Config::loadSettings($settingsPath, true);

						$reply = array(
							'result' => 'ok',
							'desc' => 1
						);
					} else {
						throw new Exception('No value specified.');
					}
					break;
				default:
					throw new Exception('No command specified.');
					break;
			}
		} catch (Exception $e) {
			$reply = array(
				'result' => 'error',
				'desc' => (string) $e
			);
		}

		$this->_helper->json($reply, true);
	}

        public function errorsAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Errors');

		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();

	}

        public function importAction()
	{
		// TODO allow user to update settings.xml file using a form. must be logged in.
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Import/Export');

		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();

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

	public function mergeXmlAction() {
	    $request = $this->getRequest();
	    
	    try {
		$file = basename($request->getParam('file', null));
		$other = basename($request->getParam('other', null));

		$filePath = APPLICATION_PATH . '/settings/environment/' . $file;
		$otherPath = APPLICATION_PATH . '/settings/environment/' . $other;

		$xml = '';
		
		if (!empty($file) && file_exists($filePath) ) {

		    if (!empty($other)) {
			$xml = Zre_File::read($filePath);
			$config = new Zend_Config_Xml($xml, null, true);

			$xml = Zre_File::read($otherPath);
			$otherConfig = new Zend_Config_Xml($xml, null, true);

			$config->merge($otherConfig);
			Zre_Config::saveSettings($config, $filePath);

			$xml = Zre_File::read($filePath);
		    } else {
			throw new Exception('Invalid destination specified.');
		    }
		} else {
		    throw new Exception('Invalid file specified.');
		}
		
		$reply = array(
		    'result' => 'ok',
		    'data' => $xml
		);
	    } catch (Exception $e) {
		$reply = array(
		    'result' => 'error',
		    'data' => (string) $e
		);
	    }

	    $this->_helper->json($reply);
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

        public function phpEnvironmentAction() {

	}

        public function runmodeAction() {
            $settingsPath = APPLICATION_PATH . '/settings/environment/settings.xml';
            $xmlString = Zre_File::read($settingsPath);
            
            $config = new Zend_Config_Xml($xmlString, null, true);
            $config = Zre_Config::parseSettings($config, $config->runmode);

            $this->view->runmode = $config->runmode->use;
            $this->view->runmodes = array(
                'dev' => 'Test',
                'production' => 'Live'
            );
        }

	public function securityKeysAction() {
		$settings = Zre_Config::getSettingsCached();

		$this->view->cryptographicSalt = isset($settings->site->cryptographicSalt) ?
							$settings->site->cryptographicSalt :
							'';
	}

	public function updatesAction() {
		
	}

	public function xmlConfigAction()
	{

		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('Configuration');
		$request = $this->getRequest();

		//@todo - This should probably be a define or something instead of hardcoded in two places
		$settingsPath = realpath(APPLICATION_PATH . '/settings/environment/settings.xml');

		$xmlString = Zre_File::read($settingsPath);
		$this->view->xml_string = $xmlString;

		$dir = APPLICATION_PATH . '/settings/environment/';
		$dirList = Zre_File::ls($dir);
		$xmlFiles = array();
		
		foreach($dirList as $filename) {
		    if (is_file($dir . $filename) && $filename != 'settings.xml') {
			$ext = pathinfo($dir . $filename, PATHINFO_EXTENSION);

			if ($ext == 'xml') $xmlFiles[] = $filename;
		    }
		}

		$this->view->xmlFiles = $xmlFiles;

		Zre_Registry_Session::set('selectedMenuItem', 'Settings');
		Zre_Registry_Session::save();

	}

}