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
 * Zre_Ui_Jquery_Tree - jQuery tree widget. Outputs javascript and html.
 * 
 * @todo finish implementing.
 *
 */
class Zre_Ui_Jquery_Tree
{
	private $_id;
	private $_datasourceUrl;
	private $_rootLink;
	
	public function __construct( $id, $datasourceUrl, $rootLink = '/' )
	{
		$this->_id = $id;
		$this->_datasourceUrl = $datasourceUrl;
		$this->_rootLink = $rootLink;
	}
	public function getScript()
	{
		$output = "
		$(document).ready( function() {
		    $('#{$this->_id}').fileTree({ 
		    	root: '{$this->_rootLink}', 
		    	script: '{$this->_datasourceUrl}',
		    	folderEvent: 'click',
		    	loadMessage: 'Loading...' 
				}, 
		    	
			    function(file) {
			        
			    });
		});
		";
		
		return $output;
	}
	public function getHtml() {
		$output = '<div id="' . $this->_id . '"></div>';
		return $output;
	}
	public function __toString()
	{
		$output = $this->getHtml();
		return $output;
	}
}
?>