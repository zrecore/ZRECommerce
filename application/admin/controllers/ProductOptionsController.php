<?php

class Admin_ProductOptionsController extends Zre_Controller_Crud_Json_Action
{
    public function init() {
	// ...Set up our dataset object.
	$this->_dataset = new Zre_Dataset_ProductOptions();
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
}