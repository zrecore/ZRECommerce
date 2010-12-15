<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Orders
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * OrdersController - Order administration controller.
 *
 */
class Admin_OrdersController extends Zend_Controller_Action {
	
	public function preDispatch() {
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() && 
			(Zre_Acl::isAllowed('orders', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')) ) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=>'true'));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		Zre_Registry_Session::set('selectedMenuItem', 'Orders');
		Zre_Registry_Session::save();
		
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->title = 'Orders';
	}
	
	public function jsonListAction() {
		
		$request = $this->getRequest();
		$data = null;
		
		try {
			$t = Zend_Registry::get('Zend_Translate');
			$settings = Zre_Config::getSettingsCached();

			$pre = $settings->db->table_name_prepend;

			$dataset = new Zre_Dataset_Orders();

			$sort = $request->getParam('sort', 'title');
			$order = $request->getParam('order', 'ASC');
			$page = $request->getParam('pageIndex', 1);
			$rowCount = $request->getParam('rowCount', 5);

			$options = array(
				'order' => $sort . ' ' . $order,
				'limit' => array(
					'page' => $page,
					'rowCount' => $rowCount
				)
			);

			$totalRecords = $dataset->listAll(null, array(
				'from' => array(
					'name' => array('u' => $dataset->getModel()->info('name')),
					'cols' => array(new Zend_Db_Expr('COUNT(*)'))
				)
			), false)->current()->offsetGet('COUNT(*)');

			$records = $dataset->getProfiles(null, $options, $pre);

			$data = array(
				'result' => 'ok',
				'totalRows' => $totalRecords,
				'data' => $records
			);
			
		} catch (Exception $e) {
			$data = array(
				'result' => 'error',
				'data' => (string) $e
			);
			Debug::log((string) $e);
		}
		$this->_helper->json($data);
	}
	
	public function jsonUpdateAction() {
		$request = $this->getRequest();
		
		try {
			$orders = new Zre_Dataset_Orders();
			$order_id = $request->getParam('order_id', null);
			$status = $request->getParam('status', null);
			
			if (isset($order_id) && isset($status)) {
				$result = (int)$orders->update(array('status' => $status), (int)$order_id);
				
				$data = array(
					'result' => 'ok',
					'data' => $result,
					'order_id' => $order_id,
					'status' => $status
				);
			} else {
				throw new Zend_Exception('Invalid parameters. No order ID or status specified.');
			}
		} catch (Exception $e) {
			$data = array(
				'result' => 'error',
				'data' => (string) $e
			);
			Debug::log((string) $e);
		}
		$this->_helper->json($data);
	}

}