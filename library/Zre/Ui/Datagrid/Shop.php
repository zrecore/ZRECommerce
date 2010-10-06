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
 * Zre_Ui_Datagrid_Shop - Displays product listings, without pagination, 
 * associated by a category (aka container) id.
 *
 */
class Zre_Ui_Datagrid_Shop
{
	private $_categoryId;
	
	public function __construct( $categoryId = 0 )
	{
		$this->_categoryId = $categoryId;
	}
	/**
	 * Return data as an associative array.
	 *
	 * @return array
	 */
	public function getData() {
		$categoryId = $this->_categoryId;
//		
//		$productData = Zre_Dataset_Product::read( $categoryId );
//		return $productData;
		$productTable = new Zre_Dataset_Model_ProductNode();
		
		$isPublished = 'yes';
		
		$select = $productTable->select()->where('published = ?', $isPublished);
		
		if (isset($this->_categoryId)) {
			$select = $select->where('container_id = ?', $categoryId);
		}
		
		$select->from( array('p' => $productTable->info('name')) , array( 'id' ));
		
		$result = $productTable->fetchAll( $select );
		
		$productIdArray = $result->toArray();

		return $productIdArray;
	}
	
	public function __toString()
	{
		
		$productIdArray = $this->getData();
		$productDataset = new Zre_Dataset_Product();
		
		$output = '
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dataTable">
			<tr	class="dataHeader">
				<td class="dataHeaderCell">Image</td>
				
				<td class="dataHeaderCell">Title</td>
				
				<td class="dataHeaderCell">Price</td>
				<td class="dataHeaderCell">Amt Left</td>

			</tr>
		';
		$intIndex = 0;
		$intModulus = 0;
		
		foreach ( $productIdArray as $productId )
		{
			$intModulus = $intIndex % 2;
			$strAlt = ($intModulus ? 'Alt' : '');
			$productData = $productDataset->read( $productId['id'] );
			if (!$productData['image']) $productData['image'] = '/images/dummy.png';
			$output .= "
			<tr class=\"dataRow$strAlt\">

				<td class=\"dataCell$strAlt textAlignCenter\">
					<a href=\"" . Zre_Template::makeLink( Zre_Template::LINK_PRODUCT, $productData['id'] ) . "\">
						<img src=\"{$productData['image']}\" alt=\"{$productData['title']}\" class=\"dataCellImage$strAlt\" />
					</a>
				</td>
				
				<td class=\"dataCell$strAlt\">
					<a href=\"" . Zre_Template::makeLink( Zre_Template::LINK_PRODUCT, $productData['id'] ) . "\"><h2>{$productData['title']}</h2></a>
					<small>Last modified: {$productData['date_modified']}</small>

					<p>{$productData['description']}</p>
				</td>

				<td class=\"dataCell$strAlt textAlignCenter\"><h3>{$productData['price']}</h3></td>
				<td class=\"dataCell$strAlt textAlignCenter\">{$productData['allotment']}</td>

			</tr>
			";
			$intIndex++;	
		}
		$output .= '
		</table>
		';
		
		return $output;
	}
}

?>