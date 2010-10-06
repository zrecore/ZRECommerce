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
 * Zre_Ui_Table_Tree - Display tree data as a table.
 *
 */
class Zre_Ui_Table_Tree extends Zend_Db_Table_Abstract 
{
	/**
	 * Internal helper function.
	 * 
	 * @param array|null $root
	 * @param array|null $path
	 */
	private function _walkTree($root = null, $path = null)
	{
		if ($root = null) {
			$root = $this->getBranchChildren(0);
		}
		
		$arrNodes = array();
		$index = 0;
		
		foreach ($root as $branch) {
			
			$thisPath = ($path === null) ? 
						('/' . $branch['branch_name'] . '/') : 
						($path . $branch['branch_name'] . '/');
			
			$key = ($branch['parent'] == 0) ? $branch['id'] : $index;
			
			$arrNodes[$key]['id'] 			= $branch['id'];
			$arrNodes[$key]['label'] 		= $branch['branch_name'];
			$arrNodes[$key]['path'] 		= $thisPath;
			$arrNodes[$key]['iconClass'] 	= 'folder';
			$arrNodes[$key]['children'] 	= array();
			
			if ($branches = $this->getBranchChildren( $branch['id'] )) {
				$arrNodes[$key]['children'] = $this->_walkTree($branches, $thisPath);
			}
			
			// Increment our index counter
			$index++;
		}
		
		// Return our result
		return $arrNodes;
	}
}
?>