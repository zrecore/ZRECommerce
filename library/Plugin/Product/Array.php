<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Plugin
 * @subpackage Product
 * @category Plugin
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Ui_Widgets_Plugin_Product_Array - Display an array of products.
 *
 */
class Plugin_Product_Array implements Plugin_Abstract
{
	private $_options = null;
	public function __construct($options=null)
	{
		$this->_options = $options;
	}
	public function setOptions($options)
	{
		$this->_options = $options;
		return true;
	}
	public function getOptions()
	{
		return $this->_options;
	}
	public function __toString()
	{
		return $this->getHtml();
	}
	public function getHtml() {
	    $reply = '';
	    if (!empty($this->_options['article_id']) )
	    {
		$options = $this->getOptions();
		$articleId = $options['article_id'];

		$products = new Zre_Dataset_Product();
		$rows = $products->listAll(
			array(
			    'article_id' => $articleId,
			    'published' => 'yes'
			),
			array(
			    'order' => 'title ASC'
			),
			false
		);

		if ($rows->count() > 0) {
		    $reply = '<ul class="ui-widget plugin-product-array">';
		    foreach ($rows as $row) {
			$reply .=
			'<li class="ui-helper-reset ui-helper-clearfix product-item">' .
			    '<a href="/shop/">' .
				'<img class="ui-helper-reset ui-helper-clearfix product-image" src="' . $row->image . '" alt="' . $row->title . '"/>' .
			    '</a>' .
			'</li>';
		    }
		    $reply .= '</ul>';
		} else {

		}
	    }

	    return $reply;
	}
	
	public function getScript() {
		
	}
	/**
	 * Outputs the configuration form
	 *
	 * @return Zend_Form
	 */
	public function getForm() {
		
	}
	
	/**
	 * Validate submitted values.
	 *
	 * @param array $params
	 */
	public function validateForm( $params ) {
		
	}
	
	/**
	 * Process the form values.
	 *
	 * @param array $params
	 */
	public function processForm( $params ) {
		
	}
	/**
	 * Install the plugin
	 *
	 */
	public function install() {
		
	}
	/**
	 * Uninstall the plugin
	 *
	 */
	public function uninstall() {
		
	}
}
?>