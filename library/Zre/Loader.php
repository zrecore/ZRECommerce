<?php
	/**
	 * Zre_Loader - Provides callback to Zend_Loader_Autoloader to handle
	 * autoloading of Zre classes beneath .../Zre
	 *
	 * @author Jack Forrest
	 */
	class Zre_Loader extends Zend_Loader {
		/**
		 * Looks class up, parsing extended module
		 *
		 * @param string|array $class
		 */
		public static function loadClass($class, $dirs = null) {
			$dir = "../library";
			$subdirs = explode('_', $class);
			foreach($subdirs as $subdir) {
				$dir .= '/' . $subdir;
			}
			$dir .= '.php';
			require_once($dir);
		}
		
		/**
		 * Attempts to load class
		 *
		 * @param string $class
		 * @return object|boolean
		 */
		public static function autoload($class) {
			try {
				return self::loadclass($class);
			} catch(Exception $e) {
				return false;
			}
		}
	}
?>