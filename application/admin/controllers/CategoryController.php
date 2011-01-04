<?php

class Admin_CategoryController extends Zre_Controller_Crud_Json_Action
{
    public function init() {
	// ...Set up our dataset object.
	$this->_dataset = new Zre_Dataset_Article_Container();
    }

    public function preDispatch() {
	$zend_auth = Zend_Auth::getInstance();
	$zend_auth->setStorage(new Zend_Auth_Storage_Session());
	$settings = Zre_Config::getSettingsCached();

	if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
	    $this->_redirect('https://' . $settings->site->url . '/admin/');
	}

	// All pages here require a valid login. Kick out if invalid.

	if (!$zend_auth->hasIdentity()) {
	    $this->_redirect('/admin/login');
	}

	$this->_helper->layout->disableLayout();
    }

    public function jsonDirAction() {
	$this->getResponse()->setHeader('Content-Type', 'application/json');
	
	$request = $this->getRequest();
	$id = $request->getParam('id');

	$categories = new Zre_Dataset_Article_Container();
	$categoryData = $categories->listAll(
		array(
		    'parent_id' => $id
		),
		array(
		    'order' => 'order_weight ASC',
		    'order ' => 'title ASC'
		),
		false
	);

	$rootCategories = array();

	foreach ($categoryData as $row) {
	    $rootCategories[] = array(
		'data' => $row->title,
		'attr' => array(
		    'id' => $row->article_container_id,
		    'parent_id' => !empty($row->parent_id) ? $row->parent_id : ''
		),
		'state' => 'closed',
		'icon' => 'folder'
	    );
	}

	$this->_helper->json($rootCategories);
    }
}