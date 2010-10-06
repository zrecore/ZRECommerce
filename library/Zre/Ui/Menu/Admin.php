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
				'Admin' => 		array('id'=>'mnuAdminIndex', 'class'=>'menuItem', 'url'=>'/admin/'),
				'Settings' => 	array('id'=>'mnuAdminIndexSettings', 'class'=>'menuItem', 'url'=>'/admin/index/settings/'),
				'Backup' => 	array('id'=>'mnuAdminIndexBackup', 'class'=>'menuItem', 'url'=>'/admin/backup/'),
				'Updates' =>	array('id'=>'mnuAdminIndexUpdates', 'class'=>'menuItem', 'url'=>'/admin/index/updates/'),
				'Errors' =>		array('id'=>'mnuAdminIndexLogs', 'class'=>'menuItem', 'url'=>'/admin/logs/'),
				'Users' =>		array('id'=>'mnuAdminUsers', 'class'=>'menuItem', 'url'=>'/admin/users/'),
				'Orders' =>		array('id'=>'mnuAdminOrders', 'class'=>'menuItem', 'url'=>'/admin/orders/'),
				'Products' =>	array('id'=>'mnuAdminProducts', 'class'=>'menuItem', 'url'=>'/admin/products/'),
				'Articles' =>	array('id'=>'mnuAdminArticles', 'class'=>'menuItem', 'url'=>'/admin/articles/'),
				'Sign Out' =>	array('id'=>'mnuSignOut', 'class'=>'menuItem', 'url'=>'/admin/login/logout/')
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