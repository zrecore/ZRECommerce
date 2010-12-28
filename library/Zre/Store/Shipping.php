<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Store
 * @subpackage Store
 * @category Store
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Store_Shipping - Provides shipping calculations and shipping distances.
 *
 */
class Zre_Store_Shipping {
	public static function logisticAdapters() {
	    $dir = BASE_PATH . '/library/Logistic/Adapter/';

	    $files = Zre_File::ls($dir);

	    $adapters = array();

	    foreach($files as $file) {

		if (is_file($dir . $file)) {
		    $name = pathinfo($dir . $file, PATHINFO_FILENAME);
		    $extension = pathinfo($dir . $file, PATHINFO_EXTENSION);

		    if ($extension == 'php' && $name !== 'Interface') {
			$adapters[] = $name;
		    }
		}
		
	    }

	    return $adapters;
	}
}
?>