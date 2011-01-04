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
		$this->view->title = 'Articles';
	}
	
	public function jsonListAction() {
		
		$request = $this->getRequest();
		$data = null;
		
		try {
			$t = Zend_Registry::get('Zend_Translate');
			$settings = Zre_Config::getSettingsCached();
			
			$dataset = new Zre_Dataset_Article();
			$categories = new Zre_Dataset_Article_Container();
			
			$sort = $request->getParam('sort', 'title');
			$order = $request->getParam('order', 'ASC');
			$page = $request->getParam('pageIndex', 1);
			$rowCount = $request->getParam('rowCount', 5);
			
			$options = array(
				'setIntegrityCheck' => false,
				'order' => 'a.' . $sort . ' ' . $order,
				'limit' => array(
					'page' => $page,
					'rowCount' => $rowCount
				),
				'from' => array(
				    'name' => array('a' => $dataset->info('name')),
				    'cols' => array(
					'*'
				    )
				),
				'leftJoin' => array(
				    'name' => array('aC' => $categories->info('name')),
				    'cols' => array('category_title' => 'aC.title'),
				    'cond' => 'aC.article_container_id = a.article_container_id'
				)
			);
			
			$totalRecords = $dataset->listAll(null, array(
				'from' => array(
					'name' => array('a' => $dataset->getModel()->info('name')),
					'cols' => array(new Zend_Db_Expr('COUNT(*)'))
				)
			), false)->current()->offsetGet('COUNT(*)');
			
			$records = $dataset->listAll(null, $options);
			foreach($records as $i => $r) {
				$records[$i]['title'] = stripslashes($records[$i]['title']);
				$records[$i]['description'] = stripslashes($records[$i]['description']);
			}
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
			$published = $request->getParam('published');
			
			$date = new Zend_Date(null, 'yyyy-MM-dd HH:mm:ss');
			$date_modified = $date->get('yyyy-MM-dd HH:mm:ss');
			
			if (!isset($article_id)) throw new Zend_Exception('No article ID specified.');
			
			$updateData = array(
				'article_id' => $article_id,
				'description' => $description,
				'title' => $article_title,
				'date_modified' => $date_modified,
				'published' => $published
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