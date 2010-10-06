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
 * Zre_Ui_Menu_Abstract - abstarct class to display an array as a menu.
 *
 */
abstract class Zre_Ui_Menu_Abstract 
{
	private $_items;
	private $_output;
	private $_separator;
	private $_selected;
	/**
	 * @var string - The 'id' attribute of this menu object.
	 */
	private $_id;
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
	public function __construct($id, $config = array(), $separator = '', $selected = '')
	{
		$this->_items = $config;
		$this->_output = '';
		$this->_id = $id;
		$this->_separator = $separator;
		$this->_selected = $selected;
	}
	/**
	 * Clears the internal 'output' string, so as to allow the output to be re-rendered when
	 * the __toString() method is called.
	 */
	public function clear()
	{
		$this->_output = '';
	}
	/**
	 * Returns the rendered output.
	 */
	public function  __toString()
	{
		if (empty($this->_output) )
		{
			$t = Zend_Registry::get('Zend_Translate');
				
			$this->_output = '<div id="' . $this->_id . '" class="menu">';
			$counter = 0;
			
			foreach($this->_items as $entryKey => $entryValue)
			{
				if (is_array($entryValue))
				{
					
					$attributes = '';
					foreach ( $entryValue as $aKey => $aVal )
					{
						if (strtolower($aKey) != 'url')
						{
							$attributes .= $aKey . '="' . $aVal . '" ';
						} else {
							$attributes .= 'href="' . $aVal . '" ';
						}
					}
					$this->_output .= '<a ' . $attributes . '>' . $t->_($entryKey) . '</a>';
					
					if ($counter < count($this->_items) - 1 )
					{
						$this->_output .= $this->_separator;
					}
					
				} elseif ($entryValue instanceof Zre_Ui_Menu_Abstract ) {
					
					// Get the toString() value.
					$this->_output .= $entryValue->__toString();
				}
				$counter++;
			}
			$this->_output .= '</div>';
		}
		return $this->_output;
	}
}
?>