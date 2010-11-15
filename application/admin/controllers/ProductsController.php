<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Products
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ProductController - Product inventory administration controller.
 * 
 */
class Admin_ProductsController extends Zend_Controller_Action
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
		if ( $zend_auth->hasIdentity() && 
			(Zre_Acl::isAllowed('products', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')) ) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=>'true'));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		
		Zre_Registry_Session::set('selectedMenuItem', 'Products');
		Zre_Registry_Session::save();
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		// TODO Auto-generated ProductsController::indexAction() default action
		$params = '';
		
		$start_index = $this->getRequest()->getParam('start_index');
		$max_per_page = $this->getRequest()->getParam('max_per_page');
		
		$params_array = array( 
			'start_index' => isset($start_index)?$start_index:0, 
			'max_per_page' => $max_per_page?$max_per_page:10
		);
		
		// Grab our key/value POST vars, for use with our datagrid links.
		foreach($params_array as $key => $value)
		{
			$params .= '/'.$key . '/' . $value;
		}
    	$url_params = $params;
    	$this->view->assign('url_params', 	$url_params);
    	$this->view->assign('start_index', 	$params_array['start_index']);
    	$this->view->assign('max_per_page', $params_array['max_per_page']);
	}
	
	public function newAction()
	{
		if (Zre_Acl::isAllowed('products', 'new')) {
			$t = Zend_Registry::get('Zend_Translate');
			$this->view->title = $t->_('Products');
			
			$this->view->assign('enable_dojo', 1);
			$this->view->assign('enable_jquery', 1);
			
			$this->view->assign('extra_jquery_css', array("scripts/jquery/jqueryFileTree/jqueryFileTree.css"));
			$this->view->assign('params', $this->getRequest()->getParams());
			
			$params = '';
			
			$start_index = $this->getRequest()->getParam('start_index');
			$max_per_page = $this->getRequest()->getParam('max_per_page');
			
			$params_array = array( 
				'start_index' => isset($start_index)?$start_index:0, 
				'max_per_page' => $max_per_page?$max_per_page:10
			);
			
			// Grab our key/value POST vars, for use with our datagrid links.
			foreach($params_array as $key => $value)
			{
				$params .= '/'.$key . '/' . $value;
			}
	    	$url_params = $params;
	    	
			$form = new Zre_Ui_Form_Product(null, '/admin/products/index' . $url_params);
			
			$is_submitted = $this->getRequest()->getParam('is_submitted');
			
			if ($is_submitted)
			{
				if ( $form->isValid($this->getRequest()->getParams()) )
				{
					$values = $form->getValues();
					$productsDataset = new Zre_Dataset_Product();
					$productsDataset->create( $values );
					
					echo "Ok!";
					$this->_redirect('/admin/products/');
				} else {
					echo "Invalid Fields.";
				}
			}
			
			$this->view->form = $form;
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	
	public function editAction()
	{
		if (Zre_Acl::isAllowed('products', 'update')) {
			$t = Zend_Registry::get('Zend_Translate');
			$this->view->title = $t->_('Products');
	
			$this->view->assign('enable_dojo', 1);
			$this->view->assign('enable_jquery', 1);
			
			$this->view->assign('extra_jquery_css', array("scripts/jquery/jqueryFileTree/jqueryFileTree.css"));
			$this->view->assign('params', $this->getRequest()->getParams());
			
			$params = '';
			
			$start_index = $this->getRequest()->getParam('start_index');
			$max_per_page = $this->getRequest()->getParam('max_per_page');
			
			$params_array = array( 
				'start_index' => isset($start_index)?$start_index:0, 
				'max_per_page' => $max_per_page?$max_per_page:10
			);
			
			// Grab our key/value POST vars, for use with our datagrid links.
			foreach($params_array as $key => $value)
			{
				$params .= '/'.$key . '/' . $value;
			}
	    	$url_params = $params;
	    	
			$form = new Zre_Ui_Form_Product(null, '/admin/products/index' . $url_params);
			
			$is_submitted = $this->getRequest()->getParam('is_submitted');
			
			if ($is_submitted)
			{
				if ( $form->isValid($this->getRequest()->getParams()) )
				{
					$values = $form->getValues();
					$productsDataset = new Zre_Dataset_Product();
					$productsDataset->update( $values['id'], $values );
	
					echo "Ok!";
					$this->_redirect('/admin/products/');
				} else {
					echo "Invalid Fields.";
				}
			} else {
	
				$id = $this->getRequest()->getParam('node_id');
				
				if ( !empty($id) )
				{
	
					$productsDataset = new Zre_Dataset_Product();
					$values = $productsDataset->read( $id );
					$form->populate( $values );
				} else {
					$this->_redirect('/admin/products/');
				}
			}
			
			$this->view->form = $form;
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	
	public function removeAction()
	{
		if (Zre_Acl::isAllowed('products', 'remove')) {
			// Remove a product, after user has confirmed this action.
			$this->view->title = 'Remove article';
			
			$t = Zend_Registry::get('Zend_Translate');
			
			$node_id = $this->getRequest()->getParam('node_id');
			
			// See if we have any listing parameters.
			$params = '';
			
			$start_index = $this->getRequest()->getParam('start_index');
			$max_per_page = $this->getRequest()->getParam('max_per_page');
			
			$params_array = array( 
				'start_index' => isset($start_index)?$start_index:0, 
				'max_per_page' => $max_per_page?$max_per_page:10
			);
			
			// Grab our key/value POST vars, for use with our datagrid links.
			foreach($params_array as $key => $value)
			{
				$params .= '/'.$key . '/' . $value;
			}
	    	$url_params = $params;
	    	$this->view->assign('url_params', 	$url_params);
	    	$this->view->assign('start_index', 	$params_array['start_index']);
	    	$this->view->assign('max_per_page', $params_array['max_per_page']);
			
			$form = new Zre_Ui_Form_Dialog_YesNoAbort();
			$form->setAction('/admin/products/remove/node_id/'.$node_id);
			
			$is_submitted = $this->getRequest()->getParam('is_submitted');
			
			// Prompt the user for a confirmation.
			if ( $is_submitted == true && isset($node_id) )
			{
				$values = $this->getRequest()->getParams();
				
				switch($values['yes_no_abort'])
				{
					case 'yes':
						
						$form = null;
						Zre_Dataset_Product::delete( $node_id );
						$this->view->assign('content', '<div class="ok">' . $t->_('Ok:') . ' ' . $t->_('Update complete.') . '</div>');
						
						break;
					case 'no':
						
						$form = null;
						$this->view->assign('content', '<div><b>' . $t->_('Cancelled:') . '</b> ' . $t->_('No update was performed.') . '</div>');
						
						break;
					case 'abort':	// This case statement intentionally left blank.
					default:
						$this->_redirect('/admin/products/');
						break;
				}
				
			}
			
			$this->view->form = $form;
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	
	public function categoriesAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('enable_jquery', 1);
			
		$this->view->assign('extra_jquery_css', array("scripts/jquery/jqueryFileTree/jqueryFileTree.css"));
		$this->view->assign('params', $this->getRequest()->getParams());
		
		$this->view->form = new Zre_Ui_Form_Product_Categories();
		
	}
	public function categoryAddAction() {
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		$containerParentId = $this->getRequest()->getParam('parent_id');
		
		if ( !isset($containerParentId) || !is_numeric($containerParentId) ) {
			$containerParentId = 0;
		}
		
		Zre_Dataset_Product::createContainer( $this->getRequest()->getParams() );
		
		$this->_redirect('/admin/products/categories/');
		
	}
	public function categoryRemoveAction() {
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		$containerId = $this->getRequest()->getParam('id');
		if ( isset($containerId) && is_numeric($containerId) ) {
			Zre_Dataset_Product::deleteContainer( $containerId );
		}
		
		$this->_redirect('/admin/products/categories/');
		
	}
	
//	public function ajaxcategoriesAction()
//	{
//		$this->view->assign('disable_cache', 1);
//		$this->_helper->layout->disableLayout();
//		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
//		
//		$rootContainerId = $this->getRequest()->getParam('dir');
//		$useDefault = false;
//		
//		if (!isset($rootContainerId) || $rootContainerId == '/') {
//			$useDefault = true;
//			$rootContainerId = '/0/';
//		}
//		$dirIds = explode('/', $rootContainerId);
//		$rootContainerId = (int) $dirIds[1];
//		
//		$rootContainerData = Zre_Dataset_Product::readContainer($rootContainerId);
//		
//		if ($useDefault == true) {
//			$output = '
//				<ul class="jqueryFileTree" style="display: none;">
//					<li class="directory collapsed">
//					<a href="#" rel="" onclick="
//						document.getElementById(\'container_id\').value=\'' . (int)$rootContainerData['id'] . '\';
//					">' . $rootContainerData['title'] . '</a>
//					</li>
//				</ul>
//			';
//		
//		} else {
//			$output .= '
//				<ul class="jqueryFileTree" style="display: none;">
//			';
//			
//			$directories = Zre_Dataset_Product::readContainerChildren( array('parent_id' => $rootContainerId) );
//			
//			foreach($directories as $directory) {
//				
//				if ($directory['id'] != $rootContainerId) {
//					$output .= '
//					<li class="directory collapsed">
//						<a href="#" rel="/' . (int)$directory['id'] . '/" onclick="
//							document.getElementById(\'container_id\').value=\'' . (int)$directory['id'] . '\';
//						">' . $directory['title'] . '</a>
//					';
//					
//					$listings = Zre_Dataset_Product::readContainerProducts( $directory['id'] );
//					foreach ($listings as $listing) {
//						if ($listing['category_id'] == $directory['id']) {
//							$output .= '
//							<li class="file ext_html">
//								<a href="#" rel="/' . (int)$directory['id'] . '/' . $listing['description'] . ']' . '" onclick="
//									document.getElementById(\'container_id\').value=\'' . $directory['id'] . '\';
//								">' . $listing['title'] . '</a>
//							</li>
//							';
//						}
//					}
//					
//					$output .= '
//					</li>
//					';
//				}
//			}
//			
//			$output .= '
//				</li>
//			</ul>';
//		}
//		
//		echo $output;
//
//	}
//	
//	public function ajaxCategoryEditAction()
//	{
//		$this->view->assign('disable_cache', 1);
//		$this->_helper->layout->disableLayout();
//		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
//		
//		$rootContainerId = $this->getRequest()->getParam('dir');
//		$useDefault = false;
//		
//		if (!isset($rootContainerId) || $rootContainerId == '/') {
//			$useDefault = true;
//			$rootContainerId = '/0/';
//		}
//		$dirIds = explode('/', $rootContainerId);
//		$rootContainerId = (int) $dirIds[1];
//		
//		$rootContainerData = Zre_Dataset_Product::readContainer($rootContainerId);
//		
//		if ($useDefault == true) {
//			$output = '
//				<ul class="jqueryFileTree" style="display: none;">
//					<li class="directory collapsed">
//					<a href="#" rel="" onclick="
//						document.getElementById(\'id\').value=\'' . (int)$rootContainerData['id'] . '\';
//						document.getElementById(\'title\').value=\'' . $rootContainerData['title'] . '\';
//						document.getElementById(\'description\').value=\'' . $rootContainerData['description'] . '\';
//						document.getElementById(\'parent_id\').value=\'' . $rootContainerData['parent_id'] . '\';
//					">' . $rootContainerData['title'] . '</a>
//					</li>
//				</ul>
//			';
//		
//		} else {
//			$output .= '
//				<ul class="jqueryFileTree" style="display: none;">
//			';
//			
//			$directories = Zre_Dataset_Product::readContainerChildren( array('parent_id' => $rootContainerId) );
//			
//			foreach($directories as $directory) {
//				
//				if ($directory['id'] != $rootContainerId) {
//					$output .= '
//					<li class="directory collapsed">
//						<a href="#" rel="/' . (int)$directory['id'] . '/" onclick="
//							document.getElementById(\'id\').value=\'' . (int)$directory['id'] . '\';
//							document.getElementById(\'title\').value=\'' . $directory['title'] . '\';
//							document.getElementById(\'description\').value=\'' . $directory['description'] . '\';
//							document.getElementById(\'parent_id\').value=\'' . $directory['parent_id'] . '\';
//						">' . $directory['title'] . '</a>
//					';
//					
//					$listings = Zre_Dataset_Product::readContainerProducts( $directory['id'] );
//					
//					foreach ($listings as $listing) {
//						if ($listing['category_id'] == $directory['id']) {
//							$output .= '
//							<li class="file ext_html">
//								<a href="#" rel="/' . (int)$directory['id'] . '/' . $listing['description'] . ']' . '" onclick="
//									document.getElementById(\'parent_id\').value=\'' . $directory['id'] . '\';
//									document.getElementById(\'title\').value=\'' . $directory['title'] . '\';
//									document.getElementById(\'description\').value=\'' . $directory['description'] . '\';
//									document.getElementById(\'parent_id\').value=\'' . $directory['parent_id'] . '\';
//								">' . $listing['title'] . '</a>
//							</li>
//							';
//						}
//					}
//					
//					$output .= '
//					</li>
//					';
//				}
//			}
//			
//			$output .= '
//				</li>
//			</ul>';
//		}
//		echo $output;
//
//	}
}