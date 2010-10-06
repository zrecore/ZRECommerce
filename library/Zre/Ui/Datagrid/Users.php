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
 * Zre_Ui_Datagrid_Users - Displays a list of user accounts. Includes support 
 * for pagination.
 *
 */
class Zre_Ui_Datagrid_Users extends Zre_Ui_Datagrid_Abstract 
{
	private $_class_identifier;
	/**
	 * Constructor.
	 * 
	 * @param array $options 
	 * 
	 * The following are valid options:
	 * 	'class_identifier' 	- the unique alphanumeric text to use for caching
	 * 	'start_index' 		- needed for caching, and pagination
	 * 	'max_per_page' 		- needed for caching, and pagination
	 * 	'disable_cache' 	- Disable cache? use '1'...otherwise, use '0'
	 *  'extra_columns' 	- Specifies info needed to add extra columns per row.
	 * 
	 * 	'position' 		- Can be 'append' or 'prepend'. Case-insensitive. 
	 * 					 Specifies where to attach the extra columns
	 * 
	 * 		'headerValues' - Indexed array containing the content to add to 
	 * 						 the extra header cells.
	 * 		
	 * 		'dataValues' - Indexed array containing the content to add to the extra data cells.
	 * 
	 * 		'bindColumns' - Specifies what value to replace the found key with.
	 * 			'%id%' - Whatever the id column is from your query results.
	 * 	
	 */
	public function __construct($options = array())
	{
		$options['class_identifier'] = $this->_class_identifier;

		$settings = Zre_Config::getSettingsCached();
		
		
		$t = Zend_Registry::get('Zend_Translate');
		
		$tbl_prepend = $settings->db->table_name_prepend;
		
		$start_index = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('start_index');
		if ($start_index < 0) $start_index = 0;
		$start_index = $start_index;
		
		$max_per_page = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('max_per_page');
		if ($max_per_page < 1 || $max_per_page > 100) $max_per_page = 10;
		$max_per_page = $max_per_page;
		
		$options['disable_cache'] = 1;
		$options['start_index'] = $start_index;
		$options['max_per_page'] = $max_per_page;
		$options['extra_columns'] = array(
			'postion' => 'append', 		// Can be 'append' or 'prepend'.
			'headerValues' => array(
				'&nbsp;',
				'&nbsp;'
			),
			'dataValues' => array(		// Values per column.
				'<a href="/admin/users/update/id/%id%/start_index/%start_index%/max_per_page/%max_per_page%">'.$t->_('Update').'</a>',
				'<a href="/admin/users/remove/id/%id%/start_index/%start_index%/max_per_page/%max_per_page%">'.$t->_('Remove').'</a>'
			),
			'bindColumns'=>array(
				'%id%'=>'id'
			)
		);
		
		$db = Zre_Db_Mysql::getInstance();
		
		$user_pstatement = $db->prepare('SELECT '.$tbl_prepend.'users.id, 
										'.$tbl_prepend.'users.name, 
										'.$tbl_prepend.'users.creation_date as Join_Date, 
										'.$tbl_prepend.'users_profile.email,
										'.$tbl_prepend.'users_profile.first_name as FName,
										'.$tbl_prepend.'users_profile.last_name as LName
										 FROM '.$tbl_prepend.'users, '.$tbl_prepend.'users_profile WHERE '.$tbl_prepend.'users_profile.user_id='.$tbl_prepend.'users.id  LIMIT :start_index,:max_per_page');
		$user_pstatement->bindValue(':start_index', $start_index, PDO::PARAM_INT);
		$user_pstatement->bindValue(':max_per_page', $max_per_page, PDO::PARAM_INT);
		
		$user_pstatement->execute();
		
		$user_array = $user_pstatement->fetchAll(PDO::FETCH_ASSOC);
		parent::__construct($user_array, $options);
	}
	
	public function __toString()
	{
		return parent::__toString();
	}
}
?>