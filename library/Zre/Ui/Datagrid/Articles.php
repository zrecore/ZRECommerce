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
 * Zre_Ui_Datagrid_Articles - Displays article listings. Includes pagination.
 *
 */
class Zre_Ui_Datagrid_Articles
{
	/**
	 * The Zend_Paginator containing the results indicated by a start page, and a max-per-page value.
	 *
	 * @var Zend_Paginator
	 */
	public $paginator = null;
	private $header_keys = null;
	private $exclude_columns = null;
	private $format_columns = null;
	
	/**
	 * Constructor. Loads up a list of articles, starting at $start_page, with $max_per_page entries per page.
	 * You can map table column names to custom text as key/value pairs assigned to $header_keys.
	 * You can exclude columns from the rendered output by specifiying an array of column names.
	 * Example: 
	 * 		$header_keys  = array('id', 'The Id', 'value', 'Some Value');
	 * 		$exclude_columns = array('password', 'created_date', 'another_column');
	 */
	public function __construct($start_page = 0, $max_per_page = 10, $header_keys = null, $exclude_columns = null, $format_columns = null)
	{
		$articles = new Zre_Dataset_Model_ArticleNode();
		
		$pagination = new Zend_Paginator( new Zend_Paginator_Adapter_DbSelect( $articles->select() ) );
		$pagination->setCurrentPageNumber($start_page);
		$pagination->setItemCountPerPage($max_per_page);
		
		$this->paginator = $pagination;
		$this->header_keys = $header_keys;
		$this->exclude_columns = $exclude_columns;
		$this->format_columns = $format_columns;
	}
	
	public function __toString()
	{
		$t = Zend_Registry::get('Zend_Translate');
		$output = '';
		$header = '';
		if (count($this->paginator))
		{
			$index = 0;
			$modulus = 0;
			foreach($this->paginator as $item)
			{
				$row = '';
				$modulus = $index % 2;
				$alt = $modulus ? 'Alt' : '';
				foreach($item as $key => $value)
				{
					if (!in_array($key, $this->exclude_columns))
					{
						if ($index == 0)
						{
							if (isset($this->header_keys)) {
								$header .= "<td class=\"dataHeaderCell\">{$this->header_keys[$key]}</td>";
							} else {
								$header .= "<td class=\"dataHeaderCell\">$key</td>";
							}
						}
						/**
						 * @todo Add additional format flags, if needed.
						 */
						$format_column = isset($this->format_columns[$key]) ? $this->format_columns[$key] : null;
						switch ($format_column) {
							case ENT_QUOTES:
								$row .= "<td class=\"dataCell$alt\">" . stripcslashes( html_entity_decode($value, ENT_QUOTES)) . "</td>";
								break;
							default:
								$row .= "<td class=\"dataCell$alt\">$value</td>";
								break;
						}
						
					}
				}
				$relative_index = ($index + (($this->paginator->getCurrentPageNumber() -  1) * $this->paginator->getItemCountPerPage())) + 1;
				$output .= "<tr class=\"dataRow$alt\"><td class=\"dataCell$alt\"><b>$relative_index.</b></td>$row<td class=\"dataCell$alt\">
							<a href=\"/admin/articles/edit/node_id/{$item['id']}/start_index/".$this->paginator->getCurrentPageNumber()."/max_per_page/".$this->paginator->getItemCountPerPage()."\">
							".$t->_('Edit')."</a>&nbsp;<a href=\"/admin/articles/remove/node_id/{$item['id']}/start_index/".$this->paginator->getCurrentPageNumber()."/max_per_page/".$this->paginator->getItemCountPerPage()."\">".$t->_('Remove')."</a></td></tr>";
				$index++;
				
			}
			
			$header = "<tr class=\"dataHeader\"><td class=\"dataHeaderCell\">&nbsp;</td>$header<td class=\"dataHeaderCell\">&nbsp;</td></tr>";
		}
		return "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"dataTable\">$header\n$output</table>";
	}
}
?>