<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Dataset
 * @subpackage Dataset
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Dataset_Abstract -  This interface defines basic "C.R.U.D" 
 * functionality for all datasets. Datasets should draw their data from the 
 * data models in {@link library/Zre/Dataset/Model}
 *
 */
abstract class Zre_Dataset_Abstract
{	
	public static function create( 	$data = array() ) {
		
	}
	public static function read( 	$data = array() ) {
		
	}
	public static function update( 	$data = array() ) {
		
	}
	public static function delete( 	$data = array() ) {
		
	}
}