<?php
/**
 * ZRECommerce e-commerce web application.
 *
 * @author ZRECommerce
 *
 * @package Default
 * @subpackage Default_Index
 * @category Controllers
 *
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * IndexController - The index page controller.
 *
 */
class IndexController extends Zend_Controller_Action {
	public function preDispatch() {
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );

		$settings = Zre_Config::getSettingsCached();

		if (Zre_Template::isHttps()) {
			$this->_redirect('http://' . $settings->site->url . '/', array('exit' => true));
		}
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->assign('disable_cache', 1); // Disable caching for this view?

		$cssBase = substr( Zre_Template::baseCssTemplateUrl(), 1 );

		$this->view->assign('extra_css', array( $cssBase . '/components/content/article.css' ));

                $articles = new Zre_Dataset_Article();
                $articleData = $articles->listAll(
                        array(
                            'published' => 'yes'
                        ),
                        array(
                            'order' => 'date_created DESC'
                        ),
                        true
                );

                $this->view->articles = $articleData;
                
		Zre_Registry_Session::set('selectedMenuItem', 'Home');
		Zre_Registry_Session::save();
	}

}