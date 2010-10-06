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
 * Zre_Ui_Datagrid_Read_Article - Displays article content.
 *
 */
class Zre_Ui_Datagrid_Read_Article
{
	private $_nodeId;
	
	public function __construct( $nodeId = null )
	{
		if (!isset($nodeId)) $nodeId = 0;
		
		$this->_nodeId = $nodeId;
	}
	/**
	 * Return article data as an associative array.
	 *
	 * @return array
	 */
	public function getData() {
		$id = $this->_nodeId;
		$node_id = $this->_nodeId;
		
		$node_table = new Zre_Dataset_Model_ArticleNode();
		$node_data = $node_table->fetchAll($node_table->select()->where('id = ?', $id));
		
		$content_table = new Zre_Dataset_Model_ArticleContent();
		$content_data = $content_table->fetchAll($content_table->select()->where('node_id = ?', $node_id));
		
		$node_array = $node_data->toArray();
		$content_array =  $content_data->toArray();
		$article_array = array_merge( $node_array[0],$content_array[0] );
		
		if (isset($article_array['description'])) {
			$article_array['description'] = stripcslashes(
												html_entity_decode( $article_array['description'], ENT_QUOTES )
											 );
		}
				
		if (isset($article_array['content'])) {
			$article_array['content'] = stripcslashes(html_entity_decode($article_array['content'], ENT_QUOTES));
		}
			
		return $article_array;
	}
	
	public function __toString()
	{
		$article_array = $this->getData();
		
		$article_array['content'] = html_entity_decode($article_array['content'], ENT_QUOTES);
		$article_array['content'] = str_replace(array('{', '}', '\"'), array('<', ' />', '"'), $article_array['content']);
		
		$article_array['date_modified'] = new Zend_Date($article_array['date_modified']);
		
		$output = '
		<div class="articleContainer">
			<h2 class="articleTitle">' . $article_array['title'] . '<br /><small>'. $article_array['date_modified']->getArpa() . '</small></h3>
		
			<p class="articleContent">' . ($article_array['use_zre_plugins'] == 'yes' ? Zre_Ui_Widgets_Plugin::search_and_insert( $article_array['content'] ) : $article_array['content'] ) . '</p>
		</div>
		';
		
		return $output;
	}
}
?>