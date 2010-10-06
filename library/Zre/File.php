<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Config
 * @category Config
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * Zre_File - Common file system operations
 *
 */
class Zre_File {
	
	/**
	 * Remove a directory
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function rmdir( $dir ) {
		if (!file_exists($dir)) return true;
	    if (!is_dir($dir) || is_link($dir)) return unlink($dir);

	    foreach (scandir($dir) as $item) {
	    	
            if ($item == '.' || $item == '..') continue;
            if (!self::rmdir($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!self::rmdir($dir . "/" . $item)) return false;
            }
        }
        return rmdir($dir);
	}
	
	public static function ls($dir) {
		$files = array();
		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != "..") {
		            $files[] = $file;
		        }
		    }
		    closedir($handle);
		}
		
		return $files;
	}
	
	public static function create($file, $data=null) {
		return file_put_contents($file, $data);
	}
	
	public static function read($file) {
		return file_get_contents($file);
	}
	
	public static function update($file, $data) {
		if (file_exists($file)) {
			$result = file_put_contents($file, $data);
		} else {
			$result = false;
		}
		return $result;
	}
	
	public static function delete($file) {
		if (file_exists($file)) {
			$result = unlink($file);
		} else {
			$result = false;
		}
		
		return $result;
	}
	
	public static function compress($file, $dest, $overwrite = true, $compressionLevel = 9) {
		// ...Requires the 'Archive_Tar' PEAR component.
		
		$archive = new Archive_Tar($dest);
		if (is_string($file) && file_exists($file)) {
			$file = array($file);
		} elseif (is_array($file)) {
			// ok
		} else {
			return false;
		}
		return $archive->create($file);
	}
	
	public static function decompress($file, $dest) {
		$result = false;
		if (file_exists($file)) {
		    $obj = new Archive_Tar($file); // name of TAR file
		} else {
		    Zre_Log::log('File does not exist: ' . $file, LOG_NOTICE);
		}
		
		if ($obj->extract($dest)) {
		    $result = true;
		} else {
			Zre_Log::log('Error extracting file(s): ' . $file, LOG_NOTICE);
		}
		
		return $result;
	}
	
	public static function download($url) {
		
	}
	
	public static function upload($parameter, $newFileName) {
		
	}
}
?>