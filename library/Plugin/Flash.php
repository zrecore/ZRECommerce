<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Plugin
 * @category Plugin
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Plugin_Flash - Loads up an swf flash object.
 *
 */
class Plugin_Flash implements Plugin_Abstract 
{
	private $_options;
	
	public function __construct($options=null) {
		$this->setOptions($options);
	}
	public function setOptions($options) {
		$this->_options = $options;
	}
	public function getOptions() {
		return $this->_options;
	}
	public function __toString() {
		$output = $this->getHtml() . '
		<script type="text/javascript">
		' . $this->getScript() . '
		</script>
		';
		
		return $output;
	}
	
	public function getScript() {
		$options = $this->getOptions();
		$flashString = '
			$("#' . $options['id'] . '").flash({
				swf: "' . $options['swf'] . '"
			});
		';
		
		return $flashString;
	}
	
	public function getHtml() {
		$options = $this->getOptions();
		$flashString = '
		<div id="' . $options['id'] . '"></div>
		';
		
		return $flashString;
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