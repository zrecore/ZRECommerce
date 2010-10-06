<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Ui
 * @subpackage Ui
 * @category Ui
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Ui_Sidebar_Menu - Sidebar menu.
 *
 */
class Zre_Ui_Sidebar_Menu
{
	private $items = array();
	
	public function __construct($items = null)
	{
		if (!isset($items))
		{
			$zend_auth = Zend_Auth::getInstance();
			$zendAuth->setStorage(new Zend_Auth_Storage_Session());
			$t = Zend_Registry::get('Zend_Translate');
			if ($zend_auth->hasIdentity())
	        {
	        	$items = array(
	        		  '<div><a href="/">'.$t->_('Home').'</a></div>',
	        		  '<div><a href="/admin/login/logout">'.$t->_('Logout').'</a></div>',
	        		  '<div><a href="/admin/">'.$t->_('Admin').'</a></div>'
	        	);
	        } else {
	        	$items = array(
	        		  '<div><a href="/">'.$t->_('Home').'</a></div>',
					  '<div><a href="/admin/login">'.$t->_('Login').'</a></div>', 
					  '<div><a href="/admin/login/register">'.$t->_('Register').'</a></div>' 
	        	);
	        }
		}
		$this->items = $items;
	}
	public function get()
	{
		return $this->items;	
	}
	
	public function set($items)
	{
		$this->items = $items;
	}
}
?>