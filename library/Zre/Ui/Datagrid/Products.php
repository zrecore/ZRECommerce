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
 * Zre_Ui_Datagrid_Products - Displays product listings. Includes pagination.
 *
 */
class Zre_Ui_Datagrid_Products 
{
	/**
	 * The Zend_Paginator containing the results indicated by a start page, and a max-per-page value.
	 *
	 * @var Zend_Paginator
	 */
	public $paginator = null;
	private $header_keys = null;
	private $exclude_columns = null;
	/**
	 * Constructor. Loads up a list of articles, starting at $start_page, with $max_per_page entries per page.
	 * You can map table column names to custom text as key/value pairs assigned to $header_keys.
	 * You can exclude columns from the rendered output by specifiying an array of column names.
	 * Example: 
	 * 		$header_keys  = array('id', 'The Id', 'value', 'Some Value');
	 * 		$exclude_columns = array('password', 'created_date', 'another_column');
	 */
	public function __construct($start_page = 0, $max_per_page = 10, $header_keys = null, $exclude_columns = null)
	{
		$dbtable= new Zre_Dataset_Model_ProductNode();
		
		$pagination = new Zend_Paginator( new Zend_Paginator_Adapter_DbSelect( $dbtable->select() ) );
		$pagination->setCurrentPageNumber($start_page);
		$pagination->setItemCountPerPage($max_per_page);
		
		$this->paginator = $pagination;
		$this->header_keys = $header_keys;
		$this->exclude_columns = $exclude_columns;
	}
	/**
	 * Returns product data
	 *
	 * @return Zend_Paginator
	 */
	public function getData() {
		return $this->paginator;
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
						$row .= "<td class=\"dataCell$alt\">$value</td>";
					}
				}

				$relative_index = ($index + (($this->paginator->getCurrentPageNumber() -  1) * $this->paginator->getItemCountPerPage())) + 1;
				$output .= "<tr class=\"dataRow$alt\"><td class=\"dataCell$alt\"><b>$relative_index.</b></td>$row<td class=\"dataCell$alt\">
							<a href=\"/admin/products/edit/node_id/{$item['id']}/start_index/".$this->paginator->getCurrentPageNumber()."/max_per_page/".$this->paginator->getItemCountPerPage()."\">
							".$t->_('Edit')."</a>&nbsp;<a href=\"/admin/products/remove/node_id/{$item['id']}/start_index/".$this->paginator->getCurrentPageNumber()."/max_per_page/".$this->paginator->getItemCountPerPage()."\">".$t->_('Remove')."</a></td></tr>";
				$index++;
				
			}
			
			$header = "<tr class=\"dataHeader\"><td class=\"dataHeaderCell\">&nbsp;</td>$header<td class=\"dataHeaderCell\">&nbsp;</td></tr>";
		}
		return "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"dataTable\">$header\n$output</table>";
	}
}
?>