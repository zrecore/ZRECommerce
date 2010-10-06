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
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Plugin_Menu_Breadcrumb - Display link to current location in a
 * breadcrumb fashion.
 *
 */
class Plugin_Menu_Breadcrumb implements Plugin_Abstract {
	private $_options = null;
	
	public function __construct($options=null) {
		
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
	
	public function __toString() {
		return $this->getHtml();
	}
	public function getHtml() {
		$t = Zend_Registry::get('Zend_Translate');
		
		$controllerFront = Zend_Controller_Front::getInstance();
		
		$moduleName = $controllerFront->getRequest()->getModuleName();
		if ($moduleName == $controllerFront->getDefaultModule()) $moduleName = '';
		
		$controllerName = $controllerFront->getRequest()->getControllerName();
		if ($controllerName == $controllerFront->getDefaultControllerName()) $controllerName = '';
		
		$actionName = $controllerFront->getRequest()->getActionName();
		if ($actionName == $controllerFront->getDefaultAction()) $actionName = '';
		
		$output = '
		<div id="menuBreadCrumb">
		<ul>
			<li class="first"><a href="/">' . $t->_('Home') . '</a></li>
		';
		if ($moduleName) {
			$output.= '
			<li><a href="/' . $moduleName . '/">' . $moduleName . '</a></li>
			';
			
			if ($controllerName) {
				
				$output .= '
				<li><a href="/' . $moduleName . '/' . $controllerName . '/">' . $controllerName . '</a></li>
				';
				
				if ($actionName) {
					
					$output .= '
					<li><a href="/' . $moduleName . '/' . $controllerName . '/' . $actionName . '/' . '">' . $actionName . '</a></li>
				';
				}
			}
		} else {
			
			if ($controllerName) {
				
				$output .= '
				<li><a href="/' . $controllerName . '/">' . $controllerName . '</a></li>
				';
				
				if ($actionName) {
					$paramsText = "/$controllerName/$actionName/";
					$actionText = $actionName;
					$id = $controllerFront->getRequest()->getParam(
						'id', 
						$controllerFront->getRequest()->getParam('article_id')
					);
					
					if (isset($id)) {
						switch ($controllerName) {
							case 'shop':
								$paramsText = Zre_Template::makeLink( Zre_Template::LINK_PRODUCT, $id );
								$product = new Zre_Dataset_Product();
								$data = $product->read( $id );
								if (isset($data)) {
									$actionText = $data->current()->title;
								}
								break;
							case 'read':
								$paramsText = Zre_Template::makeLink( Zre_Template::LINK_ARTICLE, $id );
								$article = new Zre_Dataset_Article();
								
								$data = $article->read( $id );
								if (isset($data)) {
									$actionText = $data->current()->title;
								}
								break;
						}
					}
					$output .= '
					<li><a href="' . $paramsText . '">' . $actionText . '</a></li>
					';
				}
			}
		}
		
		$output .= '
		</ul>
		</div>
		';
		
		return $output;
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