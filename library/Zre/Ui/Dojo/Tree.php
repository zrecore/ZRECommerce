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
 * Zre_Ui_Dojo_Tree - displays a dijit.Tree of data.
 *
 */
class Zre_Ui_Dojo_Tree
{
	private $_recursiveData;
	private $_id;
	private $_symbioticNodeId;
	
	public function __construct( $id, $symbioticNodeId, $data )
	{
		$this->_id = $id;
		$this->_recursiveData = $data;
		$this->_symbioticNodeId = $symbioticNodeId;
	}
	
	public function __toString()
	{
		$id = $this->_id;
		
		$recursionOutput = $this->__recursiveToString();
		
		$output  = "
		<script type=\"text/javascript\">
		var objData = 	{	label: '$id',
							id: '$id',
							items: [
								$recursionOutput
							]
						};
		var objDataStore = new dojo.data.ItemFileReadStore(objData);
		
		</script>
		
		<div dojoType=\"dijit.Tree\" id=\"mytree\" store=\"objDataStore\" label=\"TreeViewThingy\"></div>
		";
		
		return $output;
	}
	
	private function __recursiveToString( $recursiveNode = null)
	{
		if (isset($recursiveNode))
		{
			$recursiveData = $recursiveNode;
		} else {
			$recursiveData = $this->_recursiveData;
		}
		$output = '';
		$symNodeId = $this->_symbioticNodeId;
		
		foreach( $recursiveData as $key => $node )
		{
			$title = $node['title'];
			$value = $node['value'];
			
			$output .= "
			{	name: '$title', type: 'category'";
			
			if ( isset($node['children']) )
			{
				$output .= ', children: [' . $this->__recursiveChildrenToString( $node['children']) . ']';
			}
			
			$output .= '
			}
			';
			
			if ( isset($node['children']) )
			{
//				$output .= $this->__recursiveToString( $node['children'] );
			}
		}
		
		return $output;
	}
	
	private function __recursiveChildrenToString( $childData )
	{
		$output = '';
		$count = 0;
		
		foreach( $childData as $node )
		{
			$title = $node['title'];
			$value = $node['value'];
			
			$output .= "\n{name: '$title', type: 'category'}";
			if ($count < (count($childData) - 1) ) {
				$output .= ",\n";
			}
			$count++;
		}
		
		return $output;
	}
}
?>