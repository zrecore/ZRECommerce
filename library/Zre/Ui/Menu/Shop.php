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
 * Zre_Ui_Menu_Shop - The shop menu.
 *
 */
class Zre_Ui_Menu_Shop extends Zre_Ui_Menu_Abstract 
{
	public function __construct($id = null, $config = null, $separator = null, $selected = '')
	{
		if (!isset($id)) $id = 'mnuMainShop';
		if (!isset($config)) 
		{
//			$config = array(
//				'Home' => 		array('id'=>'mnuHome', 'class'=>'menuItem', 'url'=>'/'),
//				'Shop' => 		array('id'=>'mnuShop', 'class'=>'menuItem', 'url'=>'/shop'),
//				'Read' => 		array('id'=>'mnuRead', 'class'=>'menuItem', 'url'=>'/read'),
//				'Sign In' =>	array('id'=>'mnuSignIn', 'class'=>'menuItem', 'url'=>'/admin/login/')
//			);
		    $categories = new Zre_Dataset_Article_Container();

		    $data = $categories->listAll(
			array(
			    'parent_id' => array(
				'operator' => 'IS',
				'value' => new Zend_Db_Expr('NULL')
			    )
			),
			array(
			    'order' => 'order_weight ASC',
			    'order ' => 'title ASC'
			),
			false
		    );

		    $config = array(
			'Home' => array('id'=>'mnuHome', 'class'=>'menuItem', 'url'=>'/'),
		    );
 
		    foreach($data as $row) {
			
			$config[$row->title] = array(
			    'id' => 'menu_' . $row->article_container_id,
			    'class' => 'menuItem',
			    'url' => '/c/' . $row->article_container_id . '/' . Zre_Template::createSlug($row->title)
			);
		    }

		    $configKeys = array_keys($config);
			foreach($configKeys as $key) {
				if ($key == $selected) {
					$config[$key]['class'] .= ' menuItemSelected';
				}
			}
		}
		
		if (!isset($separator)) $separator = '';
		
		parent::__construct( $id, $config, $separator);
	}
}
?>