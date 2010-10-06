<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Template
 * @category Template
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Template - Provides template support.
 *
 */
class Zre_Template {
	const LINK_ARTICLE = 'article';
	const LINK_PRODUCT = 'product';
	
	/**
	 * Creates a link url. Valid $linkArea values are:
	 * 	Zre_Template::LINK_ARTICLE
	 * 	Zre_Template::LINK_PRODUCT
	 * 
	 * Specify the article or product id as $linkId
	 * 
	 * Optionally, you can specify an array of plugin to run the url 
	 * construction string through. 
	 * 
	 * Plugins will be feed the following options:
	 * 	array('linkArea' => $linkArea, 'linkId' => $linkId, 'url' => $url);
	 * 
	 * Plugins are run as a chain. In other words, the next plugin specified
	 * in the $plugins array will be feed the same options, but $url will be 
	 * the value returned for $url from the last plugin run.
	 * 
	 * @param string $linkArea
	 * @param int $linkId
	 * @param array $plugins
	 * 
	 * @return string
	 */
	public static function makeLink( $linkArea, $linkId, $plugins = null  ) {
		
		$url = '';
		
		if (isset($plugins) && is_array($plugins)) {
			foreach ($plugins as $plugin) {
				
				if ($plugins instanceof Plugin_Abstract ) {
					
					$url = $plugin->setOptions(array('linkArea' => $linkArea, 'linkId' => $linkId));
				}
			}
		} else {
			switch ( $linkArea ) {
				case Zre_Template::LINK_ARTICLE:
					$url = '/read/article/id/' . $linkId;
					break;
				case Zre_Template::LINK_PRODUCT:
					$url = '/shop/product/id/' . $linkId;
					break;
				default:
					break;
			}
		}
		return $url;
	}
	/**
	 * Return the base css template.
	 * 
	 * @return string
	 */
	public static function baseCssTemplateUrl() {
		$settings = Zre_Config::getSettingsCached();
		
		$url = '/styles/' . (string) $settings->site->template;
		
		return $url;
	}
	/**
	 * Return the base image url
	 * 
	 * @return string
	 */
	public static function baseImageUrl() {
		$settings = Zre_Config::getSettingsCached();
		
		$url = (string) $settings->site->image_folder_url;
		
		return $url;
	}
	/**
	 * Returns the base url of the website
	 *
	 * @return string
	 */
	public static function baseWebsiteUrl() {
		$settings = Zre_Config::getSettingsCached();
		
		if (self::isHttps() == true) {
			$url = 'https://' . $settings->site->url;
		} else {
			$url = 'http://' . $settings->site->url;
		}
		
		return $url;
	}
	/**
	 * Returns true if server port is on the specified ssl_port value in 
	 * settings.xml, false if it is not.
	 *
	 * @return boolean
	 */
	public static function isHttps() {
		$settings = Zre_Config::getSettingsCached();
		
		$sslPort = (int) $settings->site->ssl_port;
		
		return (bool)($_SERVER['SERVER_PORT'] == $sslPort);
	}
	/**
	 * Returns a list of article category IDs (1-level deep)
	 *
	 * @param int $baseContainerId The base container id to list.
	 * 
	 * @return array
	 */
	public static function listArticleCategories( $baseContainerId = 0 ) {
		
		$articleContainers = Zre_Dataset_Article::readContainerChildren( array('parent_id' => $baseContainerId) );
		
		return $articleContainers;
	}
	/**
	 * Returns the list of article IDs related to the specified article
	 * container id.
	 *
	 * @param int $containerId
	 * 
	 * @return array
	 */
	public static function listArticles( $containerId ) {
		$articles = Zre_Dataset_Article::readContainerArticles( $containerId );
		
		return $articles;
	}
	/**
	 * Returns a list of product category IDs (1-level deep)
	 *
	 * @param int $baseContainerId
	 * 
	 * @return array
	 */
	public static function listProductCategories( $baseContainerId = 0 ) {
		return Zre_Dataset_Product::readContainerChildren();
	}
	/**
	 * Returns a list of product IDs related to the specified product
	 * container id.
	 *
	 * @param int $baseContainerId
	 */
	public static function listProducts( $baseContainerId = 0 ) {
		return Zre_Dataset_Product::readContainerProducts( $baseContainerId );
	}
}
?>