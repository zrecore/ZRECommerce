<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Articles
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
class Admin_ArticlesController extends Zend_Controller_Action
{
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
			Zre_Acl::isAllowed('articles', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')
			) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=> true));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		
		Zre_Registry_Session::set('selectedMenuItem', 'Articles');
		Zre_Registry_Session::save();
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - list the articles
	 */
	public function indexAction()
	{
		// ArticlesController::indexAction() default action
		$this->view->title = 'Articles';
		$this->view->assign('params', $this->getRequest()->getParams());

	}
	/**
	 * Create a new article
	 */
	public function newAction()
	{
		if (!Zre_Acl::isAllowed('articles', 'new')) {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
		// Create a new article
		$this->view->title = 'New article';
		
		$this->view->assign('enable_dojo', 	true);
		$this->view->assign('enable_jquery', 1);
		
		$this->view->assign('extra_jquery_css', array("scripts/jquery/jqueryFileTree/jqueryFileTree.css"));
		
		$form = new Zre_Ui_Form_Article();
		
		$is_submitted = $this->getRequest()->getParam('is_submitted', false);
		// ... Is the form submitted?
		if ($is_submitted == true) {
			$is_valid = $form->isValid($this->getRequest()->getParams());
			
			if ($is_valid)
			{
				$values = $form->getValues();
				$article = new Zre_Dataset_Article();
				$article->create( $values );
				
				$this->_redirect('/admin/articles/');
			}
		}
		// ...Set our form.
		$this->view->form = $form;
		
	}
	/**
	 * Edit an existing article
	 */
	public function editAction() 
	{
		$article = new Zre_Dataset_Article();
		
		if (Zre_Acl::isAllowed('articles', 'update')) {
			// @todo ArticlesController::newAction() Creates a new article
			$this->view->title = 'Edit article';
			
			$this->view->assign('enable_dojo', 	1);
			$this->view->assign('enable_jquery', 1);
			
			$this->view->assign('extra_jquery_css', array("scripts/jquery/jqueryFileTree/jqueryFileTree.css"));
			$this->view->assign('params', $this->getRequest()->getParams());
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
	    	
			$form = new Zre_Ui_Form_Article(null, '/admin/articles/index'.$url_params);
			
			$form->setAction('/admin/articles/edit/');
			
			$is_submitted = $this->getRequest()->getParam('is_submitted');
			
			// ... Is the form submitted?
			if ($is_submitted == true) {
				$is_valid = $form->isValid($this->getRequest()->getParams());
				
				if ($is_valid == true)
				{
					$values = $this->getRequest()->getParams();
					$article->update( $values );
					
					// See if we have any listing parameters.
					$params = '';
					
					$start_index = $this->getRequest()->getParam('start_index');
					$max_per_page = $this->getRequest()->getParam('max_per_page');
					
					$params_array = array( 
						'start_index' => isset($start_index) ? $start_index : 0, 
						'max_per_page' => $max_per_page ? $max_per_page : 10
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
			    	
					$this->_redirect('/admin/articles/');
				}
			} else {
				
				// Not submitted... load up default values if an article is specified
				$values = $article->read( $this->getRequest()->getParam('id') );
				
				$values['description'] = stripcslashes( $values['description'] );
				
				$form->populate($values);
			}
			
			// ...Set our form.
			$this->view->form = $form;
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	/**
	 * Remove an existing article
	 */
	public function removeAction()
	{
		if (Zre_Acl::isAllowed('articles', 'remove')) {
			// Remove an article, after user has confirmed this action.
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
			$form->setAction('/admin/articles/remove/node_id/'.$node_id);
			
			$is_submitted = $this->getRequest()->getParam('is_submitted');
			
			// Prompt the user for a confirmation.
			if ( $is_submitted == true && isset($node_id) )
			{
				$values = $this->getRequest()->getParams();
				
				switch($values['yes_no_abort'])
				{
					case 'yes':
						
						$form = null;
						Zre_Dataset_Article::delete( $node_id );
						$this->view->assign('content', '<div class="ok">' . $t->_('Ok:') . ' ' . $t->_('Update complete.') . '</div>');
						
						break;
					case 'no':
						
						$form = null;
						$this->view->assign('content', '<div><b>' . $t->_('Cancelled:') . '</b> ' . $t->_('No update was performed.') . '</div>');
						
						break;
					case 'abort':	// This case statement intentionally left blank.
					default:
						$this->_redirect('/admin/articles/');
						break;
				}
				
			}
			
			$this->view->form = $form;
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	public function typesAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('enable_jquery', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		
	}
	public function categoriesAction() {
		$this->view->assign('disable_cache', 1);
		$this->view->assign('enable_jquery', 1);
			
		$this->view->assign('extra_jquery_css', array("scripts/jquery/jqueryFileTree/jqueryFileTree.css"));
		$this->view->assign('params', $this->getRequest()->getParams());
		
		$this->view->form = new Zre_Ui_Form_Article_Categories();
		
	}
	public function categoryAddAction() {
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		$containerParentId = $this->getRequest()->getParam('parent_id');
		
		if ( !isset($containerParentId) || !is_numeric($containerParentId) ) {
			$containerParentId = 0;
		}
		
		Zre_Dataset_Article::createContainer( $this->getRequest()->getParams() );
		
		$this->_redirect('/admin/articles/categories/');
		
	}
	public function categoryRemoveAction() {
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		$containerId = $this->getRequest()->getParam('id');
		if ( isset($containerId) && is_numeric($containerId) ) {
			Zre_Dataset_Article::deleteContainer( $containerId );
		}
		
		$this->_redirect('/admin/articles/categories/');
		
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
//		$rootContainerData = Zre_Dataset_Article::readContainer($rootContainerId);
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
//			$articleDirectories = Zre_Dataset_Article::readContainerChildren( array('parent_id' => $rootContainerId) );
//			
//			foreach($articleDirectories as $directory) {
//				
//				if ($directory['id'] != $rootContainerId) {
//					$output .= '
//					<li class="directory collapsed">
//						<a href="#" rel="/' . (int)$directory['id'] . '/" onclick="
//							document.getElementById(\'container_id\').value=\'' . (int)$directory['id'] . '\';
//						">' . $directory['title'] . '</a>
//					';
//					
//					$articleListing = Zre_Dataset_Article::readContainerArticles( $directory['id'] );
//					foreach ($articleListing as $listing) {
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
//		$rootContainerData = Zre_Dataset_Article::readContainer($rootContainerId);
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
//			$articleDirectories = Zre_Dataset_Article::readContainerChildren( array('parent_id' => $rootContainerId) );
//			
//			foreach($articleDirectories as $directory) {
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
//					$articleListing = Zre_Dataset_Article::readContainerArticles( $directory['id'] );
//					foreach ($articleListing as $listing) {
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
	
	public function jsonListAction() {
		
		$request = $this->getRequest();
		$data = null;
		
		try {
			$t = Zend_Registry::get('Zend_Translate');
			$settings = Zre_Config::getSettingsCached();
			
			$dataset = new Zre_Dataset_Article();
			
			$sort = $request->getParam('sort', 'title');
			$order = $request->getParam('order', 'ASC');
			
			$options = array(
				'order' => $sort . ' ' . $order
			);
			
			$records = $dataset->listAll(null, $options);
			foreach($records as $i => $r) {
				$records[$i]['title'] = stripslashes($records[$i]['title']);
				$records[$i]['description'] = stripslashes($records[$i]['description']);
			}
			$data = array(
				'result' => 'ok',
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
	
	public function jsonCreateAction() {
		$request = $this->getRequest();
		$data = null;
		
		try {
			
			$dataset = new Zre_Dataset_Article();
			$record = $request->getParams();
			
			$record['date_created'] = new Zend_Db_Expr('NOW()');
			$record['date_modified'] = new Zend_Db_Expr('NOW()');
			$data = $dataset->create($record);
		} catch (Exception $e) {
			Debug::log((string)$e);
			$data = 0;
		}
		
		$this->_helper->json($data);
	}
	
	public function jsonUpdateAction() {
		$request = $this->getRequest();
		$reply = null;
		
		try {
			
			$article_id = $request->getParam('article_id', null);
			$description = $request->getParam('description', null);
			$article_title = $request->getParam('title', null);
			
			$date = new Zend_Date(null, 'yyyy-MM-dd HH:mm:ss');
			$date_modified = $date->get('yyyy-MM-dd HH:mm:ss');
			
			if (!isset($article_id)) throw new Zend_Exception('No article ID specified.');
			
			$updateData = array(
				'article_id' => $article_id,
				'description' => $description,
				'title' => $article_title,
				'date_modified' => $date_modified
			);
			
			$dataset = new Zre_Dataset_Article();
			$result = $dataset->update(
				$updateData, 
				$article_id
			);
			
			$record = $dataset->read($article_id)->current();
			
			$reply = array(
				'result' => 'ok',
				'article_id' => $article_id,
				'date_modified' => $record->date_modified,
				'date_created' => $record->date_created,
				'data' => $result
			);
		} catch (Exception $e) {
			Debug::log((string) $e);
			$reply = array(
				'result' => 'error',
				'article_id' => $article_id,
				'date_modified' => $date_modified,
				'data' => (string) $e
			);
		}
		
		$this->_helper->json($reply);
	}
	
}