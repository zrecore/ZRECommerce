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
 * Zre_Ui_Datagrid_Abstract - Abstract class to display data in a tabular manner.
 *
 */
abstract class Zre_Ui_Datagrid_Abstract
{
	private $_output;
	private $_class_identifier = 'ZreUiDatagridAbstract';
	/**
	 * Constructor.
	 * 
	 * @param PDOStatement $pdo_fetch_all_results
	 * @param array $options 
	 * 
	 * The following are valid options:
	 * 	'class_identifier' - the unique alphanumeric text to use for caching
	 * 	'start_index' - needed for caching, and pagination
	 * 	'max_per_page' - needed for caching, and pagination
	 * 	'disable_cache' - Disable cache? use '1'...otherwise, use '0'
	 *  'extra_columns' - Specifies info needed to add extra columns per row.
	 * 		'position' - Can be 'append' or 'prepend'. Case-insensitive. 
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
	 * @todo Add pagination menu widget!
	 */
	public function __construct($pdo_fetch_all_results, $options = array())
	{
		if (isset($options['class_identifier'])) $this->_class_identifier = $options['class_identifier'];
		
		$t = Zend_Registry::get('Zend_Translate');
		$settings = Zre_Config::getSettingsCached();
		
		
		$vars = $options;
		
		$start_index = (int)$vars['start_index'];
		if ($start_index < 0) $start_index = 0;
		$start_index = $start_index;
		
		$max_per_page = (int)$vars['max_per_page'];
		if ($max_per_page < 1 || $max_per_page > 100) $max_per_page = 10;
		$max_per_page = $max_per_page;
		
		$cache_front_options = array(
			'lifetime' => (int)$settings->cache->lifetime->query,
			'automatic_serialization' => true
		);
		$cache_back_options = array( 'cache_dir' => $settings->cache->dir );
		$cache = Zend_Cache::factory('Core', 'File', $cache_front_options, $cache_back_options);
		$cache_file_name = 'sql'.Zend_Session::getId() . $this->_class_identifier . Zend_Controller_Front::getInstance()->getRequest()->getParam('controller') . Zend_Controller_Front::getInstance()->getRequest()->getParam('action').'page'.$start_index.'to'.$max_per_page;
		
		$saved_query_results = '';
		if ($vars['disable_cache'] == 1)
		{
			// Do nothing. Cache was disabled. Just output
		} else {
			// Cache allowed!
			$saved_query_results = $cache->load($cache_file_name);
		}
		if (!$saved_query_results)
		{
			$user_array = $pdo_fetch_all_results;
			
			if ($vars['disable_cache'] == 1)
			{
				// Do nothing. Cache is disabled.
			} else {
				// Cache our new output. Continue w/ new output.
				$save_sql_result = gzdeflate( serialize($user_array), 9 );
				$cache->save($save_sql_result, $cache_file_name);
			}
			
		} else {
			$user_array = unserialize( gzinflate( $saved_query_results ) );
		}
		$headerValues = null;
		$dataValues = null;
		$position = null;
		$rowIdColumn = null;
		if (isset($vars['extra_columns']))
		{
			if (isset($vars['extra_columns']['headerValues']) && count($vars['extra_columns']['headerValues']) > 0)
			{
				$headerValues = $vars['extra_columns']['headerValues'];
			}
			
			if (isset($vars['extra_columns']['dataValues']) && count($vars['extra_columns']['dataValues']) > 0)
			{
				$dataValues = $vars['extra_columns']['dataValues'];
			}
			
			if (isset($vars['extra_columns']['position']))
			{
				$position = strtolower($vars['extra_columns']['position']);
			}
			
			if (isset($vars['extra_columns']['bindColumns']['%id%']) && !empty($vars['extra_columns']['bindColumns']['%id%']))
			{
				$rowIdColumn = $vars['extra_columns']['bindColumns']['%id%'];
			}
			
			if (!($position=='append' || $position == 'prepend')) $position = 'append';
			if (!isset($rowIdColumn) || empty($rowIdColumn)) $rowIdColumn = 'id';
		}
		
		
		$content = '<table cellspacing="0" cellpadding="0" border="0" class="dataTable">';
		$index = 0;
		
		$patterns = array('%id%', '%start_index%', '%max_per_page%');
		
		
		foreach( $user_array as $index => $row)
		{
			$modulus = $index % 2;
			$alt_css = (!$modulus?'':'Alt');
			
			$replacements = array($row[$rowIdColumn], $start_index, $max_per_page);
			if ($index == 0)
			{
				$content .= '<tr class="dataHeader">';
				$column_names = array_keys($row);
				$content .= '<td class="dataHeaderCell dataHeaderCellIndex">&nbsp;</td>';
				
				if ($position == 'prepend')
				{
					foreach($headerValues as $hvalue)
					{
						$value = str_replace($patterns, $replacements, $hvalue);
						$content .= '<td class="dataHeaderCell">'.$value.'</td>';
					}
				}
				foreach($column_names as $column_name)
				{
					$content .= '<td class="dataHeaderCell">'.$t->_($column_name).'</td>';
				}
				
				if ($position == 'append')
				{
					foreach($headerValues as $hvalue)
					{
						$value = str_replace($patterns, $replacements, $hvalue);
						$content .= '<td class="dataHeaderCell">'.$value.'</td>';
					}
				}
				$content .= '</tr>';
			}
			
			$content .= '<tr class="dataRow'.$alt_css.'">';
			$content .= '<td class="dataCell dataCellIndex"><b>'.((int)$index + $start_index).'.</b></td>';
			if ($position == 'prepend')
			{
				foreach($dataValues as $dvalue)
				{
					$value = str_replace($patterns, $replacements, $dvalue);
					$content .= '<td class="dataCell">'.$value.'</td>';
				}
			}
			$column_values = array_values($row);
			foreach($column_values as $column_value)
			{
				$content .= '<td class="dataCell'.$alt_css.'">'.$column_value.'</td>';
			}
			if ($position == 'append')
			{
				foreach($dataValues as $dvalue)
				{
					$value = str_replace($patterns, $replacements, $dvalue);
					$content .= '<td class="dataCell">'.$value.'</td>';
				}
			}
			$content .= '</tr>';
		
			$index++;
		}
		$content .= '</table>';
		$this->_output = $content;
	}
	
	public function __toString()
	{
		return $this->_output;
	}
}
?>