<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Default
 * @subpackage Default_Read
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ReadController
 * 
 * @author
 * @version 
 */
class ReadController extends Zend_Controller_Action {
	public function preDispatch() {
		$settings = Zre_Config::getSettingsCached();
		
		if (Zre_Template::isHttps()) {
			$this->_redirect('http://' . $settings->site->url . '/read/', array('exit' => true));
		}
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		$cssBase = substr( Zre_Template::baseCssTemplateUrl(), 1 );
		$this->view->assign('disable_cache', 1);
		$this->view->assign('extra_css', array( $cssBase . '/components/content/article.css' ));
		
		$node_id = $this->getRequest()->getParam('id');
		if (!isset($node_id)) $node_id = 0;
		
		$output = new Zre_Ui_Datagrid_Read();
		$this->view->assign('content', $output->__toString() );
		$this->view->assign('params', $this->getRequest()->getParams());
		
		Zre_Registry_Session::set('selectedMenuItem', 'Read');
		Zre_Registry_Session::save();
		
	}
	/**
	 * Article read action.
	 *
	 */
	public function articleAction()
	{
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		$cssBase = substr( Zre_Template::baseCssTemplateUrl(), 1 );
		
		$this->view->assign('extra_css', array( $cssBase . '/components/content/article.css' ));
		
		// Read an article
		$article = new Zre_Dataset_Article();
		$id = $this->getRequest()->getParam('id');
        
        // ...If no node_id was specified, re-route back to index action.
		if (!isset($id))
		{
			$this->_redirect('/read/');
		}
		// ...Is this a valid ID?
		if (is_numeric($id) && is_int($id)) {
			$records = $article->read( $id );
			
			// ...Is this a valid record?
			if ($records->count() > 0) {
				$data = $records->current()->toArray();
				$this->view->data = $data;
			} else {
				// ...No, redirect.
				$this->_redirect('/read/');
			}
		} else {
			// ...No, redirect.
			$this->_redirect('/read/');
		}
		
		Zre_Registry_Session::set('selectedMenuItem', 'Read');
		Zre_Registry_Session::save();
	}
	/**
	 * Article comment view action.
	 *
	 */
	public function commentAction()
	{
		// @todo Read an article comment
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		
		Zre_Registry_Session::set('selectedMenuItem', 'Read');
		Zre_Registry_Session::save();
	}

}