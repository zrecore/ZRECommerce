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
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Ui_Menu - Generic menu creator.
 *
 */
class Zre_Ui_Menu extends Zre_Ui_Menu_Abstract 
{
	/**
	 * Constructor. Creates a menu using array items.
	 * 
	 * @param string $id - The 'id' attribute to use for this menu.
	 * @param array $config - The array of key/value pairs.
	 * 
	 * Example:
	 * 		$id = 'someMenu123';
	 * 		$config = array(
	 * 			'Menu Item' => 		array('id'=>'mnu1', 'url'=>'www.123.com'),
	 * 			'Another Item' => 	array('id'=>'mnuAnother', 'url'=>'www.foobar.org'),
	 * 			'Something Else' => array('id'=>'blipity123', 'url'=>'https://www.example.net', 'class'=>'someClass', 'style'=>'text-align: left; color: #ff00ff;'),
	 * 			'An Entry' => 		$someSubMenu
	 * 		);
	 * 
	 * Each item must have an array of values attached. All key/value pairs within the Value array that are neither 'id' nor 'url'
	 * will be added to the item's element tag as attributes. (The 'id' key/value pair is of course, added to the attributes as well.)
	 * 
	 * The 'url' will be used to set the href attribute of the anchor tag.
	 * 
	 * $someSubMenu is an object that implements Zre_Ui_Menu_Abstract, allowing for sub-menus.
	 */
	public function __construct($id, $config, $separator = null)
	{
		parent::__construct($id, $config, $separator);
	}
}
?>