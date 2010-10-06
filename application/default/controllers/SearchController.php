<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Default
 * @subpackage Default_Search
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * ShopController - Browses product inventory.
 * 
 */
class SearchController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated SearchController::indexAction() default action
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		
		Zre_Registry_Session::set('selectedMenuItem', '');
		Zre_Registry_Session::save();
	}

}
?>

