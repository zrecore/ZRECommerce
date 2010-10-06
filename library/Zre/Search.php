<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Search
 * @category Search
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_Search - Search and Index using Zend_Search_Lucene
 *
 */
class Zre_Search {
	/**
	 * Internal search adapter object
	 *
	 * @var Zre_Search_Adapter_Interface
	 */
	private static $_searchObject;
	/**
	 * Enter description here...
	 *
	 * @param string $keyword The keyword to search for
	 * @return array The results
	 */
	public static function search( $keyword, $options = null ) {
		
		$searchObj = self::getSearchObject();
		$data = $searchObj->search( $keyword, $options );
		
		return $data;
	}
	/**
	 * Index articles and product listings
	 *
	 */
	public static function index() {
		$searchObj = self::getSearchObject();
		$searchObj->index();
	}
	/**
	 * Flush search index records
	 *
	 */
	public static function flush() {
		$searchObj = self::getSearchObject();
		$searchObj->flush();
	}
	
	/**
	 * Return the index directory path.
	 *
	 * @return string
	 */
	public function getIndexDirectory() {
		$searchObj = self::getSearchObject();
		return $searchObj->getIndexDirectory();
	}
	/**
	 * Returns the internal search object.
	 *
	 * @return Zre_Search_Adapter_Interface
	 */
	public static function getSearchObject() {
		if (!isset(self::$_searchObject)) {
			$settings = Zre_Config::getSettingsCached();
			
			$searchAdapterName = (string) $settings->search->engine;
			if (!$searchAdapterName) $searchAdapterName = 'Lucene';
			$searchAdapter = 'Zre_Search_Adapter_' . $searchAdapterName;
			
			$searchAdapter = new $searchAdapter;
			
			if ($searchAdapter instanceof Zre_Search_Adapter_Interface ) {
				
				self::$_searchObject = $searchAdapter;
				
			} else {
				self::$_searchObject = null;
			}
		}
		
		return self::$_searchObject;
	}
	
	public static function setSearchObject ( $searchObject ) {
		if ($searchObject instanceof Zre_Search_Adapter_Interface ) {
			
			self::$_searchObject = $searchObject;
			
			return true;
		} else {
			return false;
		}
	}
}
?>