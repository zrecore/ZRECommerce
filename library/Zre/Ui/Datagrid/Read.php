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
 * Zre_Ui_Datagrid_Read - Displays article listings, without pagination, but with search flags.
 *
 */
class Zre_Ui_Datagrid_Read
{
	private $_categoryId;
	private $_resourceType;
	private $_published;
	
	private $_data;
	
	public function __construct( $categoryId = null, $published = 'yes', $resourceType = 'latest') {
		if (!isset($categoryId)) $categoryId = 0;
		
		$this->_categoryId = $categoryId;
		$this->_resourceType = $resourceType;
		$this->_published = $published;
		
		$this->_data = null;
	}
	
	/**
	 * Returns an array of Dataset data.
	 *
	 * @return array;
	 */
	public function getData() {
		
		if (!isset($this->_data)) {
			
			$articles_node_table = new Zre_Dataset_Model_Article();
	       	
	       	$select = $articles_node_table->select();
			$select = $select->where('article_container_id = ?'	, $this->_categoryId, PDO::PARAM_INT);
			
			if ( isset($this->_published) ) 	$select = $select->where( 'published = ?'		, $this->_published );
			if ( isset($this->_resourceType) ) 	$select = $select->where( 'resource = ?'		, $this->_resourceType );
			
			$select = $select->order(array('date_created DESC'));
	       								  
	       	$articles_node_data = $articles_node_table->fetchAll( $select );
	       	$article_node_array = $articles_node_data->toArray();
	       	
	       	foreach($article_node_array as $index => $article) {
	       	
		       	if (isset($article_node_array[$index]['description'])) {
					$article_node_array[$index]['description'] = stripcslashes(
															html_entity_decode( $article_node_array[$index]['description'], ENT_QUOTES )
														 );
		       	}
		       	
				if (isset($article_node_array[$index]['content'])) {
					$article_node_array[$index]['content'] = stripcslashes( html_entity_decode($article_node_array[$index]['content'], ENT_QUOTES));
				}
	       	}
	       	$this->_data = $article_node_array;
		}

       	return $this->_data;
	}
	
	public function __toString() {
		$output = '';
       	
		$article_node_array = $this->getData();
       	
		foreach ($article_node_array as $node_values)
		{

			$output .= '
			<div>
				<div>
					<h3 class="content_title">'.$node_values['title'].'</h3>
					<small>Last edited - '.$node_values['date_modified'].'</small>
				</div>
				
				<table cellspacing="16px" cellpadding="0" border="0" class="content_text">
					<tr>
						<td>
							<img src="'.$node_values['image'].'" alt="Article Image" />
						</td>
						<td>
							'.$node_values['description'].' <a href="/read/article/article_id/'.$node_values['id'].'">...Read</a>
						</td>
					</tr>
				</table>
			</div>';
		}
		
		return $output;
	}
}
?>