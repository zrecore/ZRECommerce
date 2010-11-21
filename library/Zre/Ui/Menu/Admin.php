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
 * Zre_Ui_Menu_Admin - The administrative menu.
 *
 */
class Zre_Ui_Menu_Admin extends Zre_Ui_Menu_Abstract 
{
	public function __construct($id = null, $config = null, $separator = '', $selected = '')
	{
		if (!isset($id)) $id = 'mnuAdmin';
		if (!isset($config)) 
		{
			$config = array(
				'View Site' => array('id'=>'mnuViewSite', 'class'=>'menuItem', 'url'=>'/'),
				'Sign Out'  =>	array('id'=>'mnuSignOut', 'class'=>'menuItem', 'url'=>'/admin/login/logout/')
			);
			
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