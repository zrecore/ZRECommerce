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
 * Plugin - Parses a string, and replaces all plugin tags with 
 * the output of the specified plugin(s).
 *
 */
class Plugin
{
	public static $tag_name = 'plugin';
	public static $root_namespace = 'Plugin_';

	public static $view = null;
	
	public static function inject($input)
	{
		$pattern = array('/<' . self::$tag_name . ' [ -~\n]*?\/>/');

		$new_input = preg_replace_callback($pattern, function($matches){
		    $xml_doc = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><document>' . $matches[0] . '</document>');

		    //Try and load up the plugin type.
		    $tag = Plugin::$tag_name;
		    $namespace = Plugin::$root_namespace;
		    
		    try {
			$attribs = array();
			foreach($xml_doc->{$tag}[0]->attributes() as $key => $value)
			{
				$attribs[$key] = (string)$value;
			}

			$class_name = $namespace . $attribs['type'];

			$compiled = new $class_name;
			$compiled->setOptions( $attribs );

		    } catch (Exception $e) {
			$compiled = $matches[0];
		    }
		    return $compiled;
		}, $input);
		return $new_input;
	}
	/**
	 * Get the view object.
	 * @return Zend_View_Abstract
	 */
	public static function getView() {
	    return self::$view;
	}
	/**
	 * Set the view object.
	 * @param Zend_View_Abstract $view
	 */
	public static function setView($view) {
	    self::$view = $view;
	}
	
	/**
	 * Returns a tree array of all plugins found in the Plugin library.
	 *
	 * @return array
	 */
	public static function probe( $baseDir = null ) {
		$result = array();
		$handle = null;
		$path = '../' . DIRECTORY_SEPARATOR . 
						'library' . DIRECTORY_SEPARATOR;
		$realPath = realpath($path);
		
		if (!isset($baseDir)) {
			$baseDir = realpath($path . 'Plugin' . DIRECTORY_SEPARATOR);
		}
		
		$handle = opendir($baseDir);

		if ($handle) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && $file[0] != ".") {
		        	
		        	$currentItemPath = $baseDir . DIRECTORY_SEPARATOR . $file;
		        	
		        	$isDir = is_dir( $currentItemPath );
		        	$isFile = is_file( $currentItemPath );
		            
		        	if ($isDir == 1) {
		        		$result = array_merge( $result, self::probe( $currentItemPath ));
		        		
		        	} else {
		        		if ($isFile == 1) {
		        			// ...Is it a php class?
		        			$className = str_ireplace( 
		        							DIRECTORY_SEPARATOR, 
		        							'_', 
		        							str_ireplace( 
		        								$realPath . DIRECTORY_SEPARATOR, 
		        								'', 
		        								$baseDir )
		        							) . '_' . substr( $file, 0, strpos( $file, '.php' ) );
		        			
		        			try {
		        				if (class_exists($className)) {
		        					// ...It's a real class, make sure it's a plugin.
		        					$pluginAbstract = 'Plugin_Abstract';
		        					$class = new $className();
		        					
		        					if( $class instanceof $pluginAbstract ) {
		        						$result[ str_ireplace('Plugin_','',$className) ] = array( 'path' => $currentItemPath );
		        					}
		        					
		        				}
		        			} catch (Exception $e) {
		        				// ...Do nothing
		        			}
		        		}
		        	}
		            
		        }
		    }
		    closedir($handle);
		}
		return $result;
	}
}

?>