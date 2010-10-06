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
 * Zre_Search_Adapter_Interface - All search adapters must implement this 
 * interface.
 *
 */
interface Zre_Search_Adapter_Interface {
	/**
	 * Search class adapter interface. All search adapters must
	 * implement this interface.
	 *
	 * @param string $keyword The keyword string to search by.
	 * @param array|null $options Array of options to be used by the adapter.
	 * 
	 * @return array The result data.
	 */
	public function search( $keyword, $options = null );
	/**
	 * Index data, save index data to the specified folder.
	 */
	public function index();
	/**
	 * Flush search index data
	 *
	 */
	public function flush();
	/**
	 * Return the index object
	 *
	 * @return mixed
	 */
	public function getIndexObject();
	/**
	 * Set the internal index object
	 *
	 * @param mixed $object
	 */
	public function setIndexObject( $object );
	/**
	 * Return the keyword query object
	 *
	 * @return mixed
	 */
	public function getKeywordObject();
	/**
	 * Set the internal keyword query object
	 *
	 * @param unknown_type $object
	 */
	public function setKeywordObject( $object );
	/**
	 * Return the index directory path.
	 *
	 * @return string
	 */
	public function getIndexDirectory();
}
?>