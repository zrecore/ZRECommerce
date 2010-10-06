<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Search
 * @subpackage Search_Adapter
 * @category Search
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_Search_Adapter_Lucene - Uses the Zend_Search_Lucene class to search
 * and index.
 *
 */
class Zre_Search_Adapter_Lucene implements Zre_Search_Adapter_Interface {
	const INDEX_FILE = 'Lucene';
	private $searchIndexObject;
	private $keyword;
	/**
	 * Search by keyword
	 *
	 * @param string $keyword
	 * @param array $options
	 * @return array
	 */
	public function search( $keyword, $options = null ) {
		/**
		 * @todo run Zend_Search_Lucene search
		 */
		
		try {
			$index = $this->getIndexObject();
			$this->setKeywordObject( $keyword );
			
			$results = $index->find( $this->getKeywordObject() );
			
			return $results;
			
		} catch (Exception $e) {
			return null; 
		}
	}
	/**
	 * Flush search index data.
	 *
	 * @return boolean
	 */
	public function flush() {
		$searchIndexFile = $this->getIndexDirectory();
		if ( file_exists( $searchIndexFile ) ) {
			
			Zre_File::rmdir( $searchIndexFile );
		}
	}
	
	/**
	 * Return the internal Zend_Search_Lucene object
	 *
	 * @return Zend_Search_Lucene
	 */
	public function getIndexObject() {
		if (!isset($this->searchIndexObject)) {
			
			$indexFolder = $this->getIndexDirectory();
			$index = Zend_Search_Lucene::open( $indexFolder );
			$this->searchIndexObject = $index;
		}
		
		return $this->searchIndexObject;
	}
	/**
	 * Set the internal Zend_Search_Lucene object
	 *
	 * @param Zend_Search_Lucene $object
	 */
	public function setIndexObject( $object ) {
		$this->searchIndexObject = $object;
	}
	/**
	 * Returns the keyword object used.
	 * 
	 * @return Zend_Search_Lucene_Search_Query
	 */
	public function getKeywordObject() {
		return $this->keyword;
	}
	/**
	 * Enter description here...
	 *
	 * @param string $keyword
	 * @return Zend_Search_Lucene_Search_Query
	 */
	public function setKeywordObject( $keyword ) {
		if (is_string($keyword)) {
			$this->keyword = Zend_Search_Lucene_Search_QueryParser::parse( $keyword );
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Index all products and articles
	 *
	 * @return boolean;
	 */
	public function index() {
		/**
		 * Run our index routine
		 */
		$settings = Zre_Config::getSettingsCached();
		
		Zend_Application::setPhpSettings(array('memory_limit' => $settings->site->memory_limit));
		Zend_Application::setPhpSettings(array('max_execution_time' => $settings->site->max_execution_time));
		
		// ...Create our index
		$indexFolder = $this->getIndexDirectory();
		
		try {
			$index = Zend_Search_Lucene::open( $indexFolder );
		} catch (Exception $e) {
			$index = Zend_Search_Lucene::create( $indexFolder );
		}
		
		// ...Grab our documents
		$index = $this->_getArticleCategories( 0, $index );
		$index = $this->_getProductCategories( 0, $index );
		
		$index->commit();
		$index->optimize();
		
		return true;
	}
	/**
	 * Return the index directory path.
	 *
	 * @return string
	 */
	public function getIndexDirectory() {
		$settings = Zre_Config::getSettingsCached();
		$indexFolder = (string) $settings->search->index_directory . DIRECTORY_SEPARATOR . self::INDEX_FILE;
		
		return $indexFolder;
	}
	
	/**
	 * Adds articles and article categories to the index object
	 *
	 * @param int $containerId
	 * @param Zend_Search_Lucene $indexObject
	 * @return Zend_Search_Lucene
	 */
	private function _getArticleCategories( $containerId, $indexObject ) {
		
		$containerChildren = Zre_Dataset_Article::readContainerChildren( array('parent_id' => $containerId) );
//		$containerInfo = Zre_Dataset_Article::readContainer( $containerId );
		$containerItems = Zre_Dataset_Article::readContainerArticles( $containerId );

		foreach ($containerChildren as $child) {
			if ( $child['id'] != $containerId ) {
				$indexObject = $this->_getArticleCategories( $child['id'], $indexObject );
			}
		}

//		$docContainerInfo = new Zend_Search_Lucene_Document();
//		
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unIndexed('id', $containerInfo['id']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unIndexed('parent_id', $containerInfo['parent_id']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::keyword('title', $containerInfo['title']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unStored('description', $containerInfo['description']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unIndexed('date', $containerInfo['date']) );
//		
//		$indexObject->addDocument( $docContainerInfo );
		
		foreach( $containerItems as $item ) {
			$docContainerItems[$item['id']] = new Zend_Search_Lucene_Document();
			
			$itemData = Zre_Dataset_Article::read( $item['id'] );
			
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('id', $itemData['id']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('container_id', $itemData['container_id']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('resource', $itemData['resource']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('published', $itemData['published']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('item_type', 'article') );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::keyword('title', $itemData['title']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unStored('description', $itemData['description']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('date_created', $itemData['date_created']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('date_modified', $itemData['date_modified']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('image', $itemData['image']) );
			
			$indexObject->addDocument( $docContainerItems[$item['id']] );
			
		}

		return $indexObject;
	}
	/**
	 * Adds products and product categories to the index object
	 *
	 * @param int $containerId
	 * @param Zend_Search_Lucene $indexObject
	 * @return Zend_Search_Lucene
	 */
	private function _getProductCategories( $containerId, $indexObject ) {
		
		$containerChildren = Zre_Dataset_Product::readContainerChildren( array('parent_id' => $containerId) );
//		$containerInfo = Zre_Dataset_Product::readContainer( $containerId );
		$containerItems = Zre_Dataset_Product::readContainerProducts( $containerId );

		foreach ($containerChildren as $child) {
			if ( $child['id'] != $containerId ) {
				$indexObject = $this->_getProductCategories( $child['id'], $indexObject );
			}
		}

//		$docContainerInfo = new Zend_Search_Lucene_Document();
//		
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unIndexed('id', $containerInfo['id']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unIndexed('parent_id', $containerInfo['parent_id']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::keyword('title', $containerInfo['title']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unStored('description', $containerInfo['description']) );
//		$docContainerInfo->addField( Zend_Search_Lucene_Field::unIndexed('date', $containerInfo['date']) );
//		
//		$indexObject->addDocument( $docContainerInfo );
//		
		foreach( $containerItems as $item ) {
			$docContainerItems[$item['id']] = new Zend_Search_Lucene_Document();
			
			$itemData = Zre_Dataset_Product::read( $item['id'] );
			
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('id', $itemData['id']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('container_id', $itemData['container_id']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('published', $itemData['published']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('item_type', 'product') );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::keyword('title', $itemData['title']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unStored('description', $itemData['description']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('date_created', $itemData['date_created']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('date_modified', $itemData['date_modified']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('image', $itemData['image']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('price', $itemData['price']) );
			$docContainerItems[$item['id']]->addField( Zend_Search_Lucene_Field::unIndexed('image', $itemData['image']) );
			
			$indexObject->addDocument( $docContainerItems[$item['id']] );
			
		}
		
		return $indexObject;
	}
}
?>