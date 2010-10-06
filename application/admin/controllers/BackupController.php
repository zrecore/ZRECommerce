<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Backup
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Admin_BackupController - "Back-up" Administration Controller.
 *
 */
class Admin_BackupController extends Zend_Controller_Action {
	
	public function preDispatch() {
		// preDispach, make sure we are logged in.
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() && 
			Zre_Acl::isAllowed('backup', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')
			) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=> true));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		// ...Enable the jQuery javascript library
		$this->view->assign('enable_jquery', 1);
		
		// ...Disable cache on this page
		$this->view->assign('disable_cache', 1);
		
		Zre_Registry_Session::set('selectedMenuItem', 'Backup');
		Zre_Registry_Session::save();
	}
	
	public function indexAction() {
		/**
		 * @todo List existing backup entries from MySQL table
		 */
		$backupArchives = Zre_File::ls('../backup');
		
		$this->view->backupArchives = $backupArchives;
	}
	public function uploadAction() {
		/**
		 * @todo Upload Tap ARchive to appropriate "date" folder
		 * @todo Record to MySQL table
		 */
	}
	public function importAction() {
		/**
		 * @todo Move selected Tape ARchive to import folder.
		 * @todo Extract content.
		 * @todo Import settings.xml
		 * @todo Import SQLite database data
		 * @todo Import MySQL database data
		 * @todo Record import to MySQL table
		 */
	}
	
	public function exportAction() {
		
	}
	public function downloadAction() {
		/**
		 * @todo Disable layout helper.
		 * @todo Download Tape ARchive indicated by selected backup entry.
		 */
		try {
			$this->_helper->layout->disableLayout();
			$backupId = $this->getRequest()->getParam('id');
			
			if ($backupId) {Zre_Config::downloadBackup($backupId);}
		} catch (Exception $e) {
			
		}
		exit;
	}
	
	public function backupAction() {
		$this->view->content = Zre_Config::systemBackup();
	}
	
	public function ajaxDeleteAction() {
		$id = $this->getRequest()->getParam('id');
		
		echo Zend_Json::encode(Zre_Config::deleteBackup($id));
		exit;
	}
}