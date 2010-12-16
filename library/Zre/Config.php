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
 * Zre_Config - Generates, loads, saves, and caches our settings.xml file
 *
 */
require_once 'Zend/Config.php';
require_once 'Zend/Config/Xml.php';
require_once 'Zend/Session.php';
require_once 'Zend/Session/Namespace.php';

	class Zre_Config  {
		
		const DEFAULT_NAMESPACE = "ZRE_CONFIG";
		public static function getDefaultConfig ()
		{
			$config = new Zend_Config(array(), true);
			$config->production = array();
			$config->dev = array();
			$config->runmode = array();
			
			// Extend production settings from dev by default.
			$config->setExtend('dev', 'production');
			
			// What should we use? dev or production?
			$config->runmode->use = 'dev';
			$config->runmode->base_path = realpath('../');
			
			$config->production->db = array();
			$config->production->db->hostname = 'localhost';
			$config->production->db->username = 'root';
			$config->production->db->password = '';
			$config->production->db->database = 'default_database';
			$config->production->db->table_name_prepend = 'zre_';
			$config->production->db->sqlite_directory = '%base_path%/application/settings/database/sqlite';
			
			$config->production->cache = array();
			$config->production->cache->dir = '%base_path%/application/cache';
			
			$config->production->search = array();
			$config->production->search->engine = 'Lucene';
			$config->production->search->index_directory = '%base_path%/tmp';
			
			$config->production->cache->lifetime = array();
			$config->production->cache->lifetime->ui = '60';
			$config->production->cache->lifetime->query = '60';
			
			$config->production->site = array();
			$config->production->site->dir = '%base_path%';
			$config->production->site->url = 'localhost';
			$config->production->site->enable_ssl = 'yes';
			$config->production->site->local = 'en_US';
			$config->production->site->title = 'Website Title';
			$config->production->site->template = 'default';
			$config->production->site->jqueryui_theme = 'smoothness';
			$config->production->site->currency = 'USD';
			$config->production->site->session_timeout = 900;
			$config->production->site->timezone = 'America/Los_Angeles';
			$config->production->site->registration_enabled = 'yes';
			$config->production->site->memory_limit = '64M';
			$config->production->site->max_execution_time = '30';
			$config->production->site->captcha = array();
			$config->production->site->captcha->label = 'Type the text you see below';
			$config->production->site->captcha->wordLen = 4;
			$config->production->site->captcha->timeout = 100;
			$config->production->site->captcha->font = '%base_path%/application/settings/fonts/FreeSans.ttf';
			$config->production->site->captcha->fontSize = 30;
			$config->production->site->captcha->height = 60;
			$config->production->site->captcha->imgDir = '%base_path%/public/images/captcha';
			$config->production->site->captcha->imgUrl = '/images/captcha';
			$config->production->site->acl_file = realpath('../application/settings/acl/default.xml');
			$config->production->site->ssl_port = 443;
			$config->production->site->image_folder_url = '/images';
			/**
			 * Dev merchant settings.
			 * Fill in each merchant entry with relevant info, such as merchant id, etc...
			 */
			$config->dev->merchant = array();
			$config->dev->merchant->adapter = 'Cybersource';
			$config->dev->merchant->google = array();
			$config->dev->merchant->google->merchant_id = '000000000000000';
			$config->dev->merchant->google->merchant_key = 'ffffffffffffffffffffff';
			$config->dev->merchant->paypal = array();
			$config->dev->merchant->paypal->api_user_name = 'atest123_api1.fake.com';
			$config->dev->merchant->paypal->api_password = 'fffffffffffffff';
			$config->dev->merchant->paypal->api_signature = '000000000000000';
			$config->dev->merchant->paypal->authorizing_account_emailaddress = 'email@example.com';
			$config->dev->merchant->paypal->api_endpoint_uri = 'https://api-3t.sandbox.paypal.com/nvp';
			$config->dev->merchant->paypal->api_test_credit_card_number = 'xxxxxxxxxxxxxxxx';
			$config->dev->merchant->paypal->api_test_expiration_month = 'MM';
			$config->dev->merchant->paypal->api_test_expiration_month = 'YYYY';
			$config->dev->merchant->cybersource = array();
			$config->dev->merchant->cybersource->merchant_id = 'atest123_api1.fake.com';
			$config->dev->merchant->cybersource->transaction_key = 'fffffffffffffff';
			$config->dev->merchant->cybersource->wsdl_url = '000000000000000';
			/**
			 * Dev shipping settings.
			 * Fill in each shipping entry with relevant info, such as api key, etc...
			 */
			$config->dev->shipping = array();
			$config->dev->shipping->ups = array();
			$config->dev->shipping->usps = array();
			$config->dev->shipping->fedex = array();
			/**
			 * Production merchant settings.
			 * Fill in each merchant entry with relevant info, such as merchant id, etc...
			 */
			$config->production->merchant = array();
			$config->production->merchant->adapter = 'Cybersource';
			$config->production->merchant->google = array();
			$config->production->merchant->google->merchant_id = '000000000000000';
			$config->production->merchant->google->merchant_key = 'ffffffffffffffffffffff';
			$config->production->merchant->paypal = array();
			$config->production->merchant->paypal->api_user_name = 'atest123_api1.fake.com';
			$config->production->merchant->paypal->api_password = 'fffffffffffffff';
			$config->production->merchant->paypal->api_signature = '000000000000000';
			$config->production->merchant->paypal->authorizing_account_emailaddress = 'email@example.com';
			$config->production->merchant->paypal->api_endpoint_uri = 'https://api-3t.paypal.com/nvp';
			$config->production->merchant->cybersource = array();
			$config->production->merchant->cybersource->merchant_id = 'atest123_api1.fake.com';
			$config->production->merchant->cybersource->transaction_key = 'fffffffffffffff';
			$config->production->merchant->cybersource->wsdl_url = '000000000000000';
			/**
			 * Production shipping settings.
			 * Fill in each shipping entry with relevant info, such as api key, etc...
			 */
			$config->production->shipping = array();
			$config->production->shipping->ups = array();
			$config->production->shipping->usps = array();
			$config->production->shipping->fedex = array();
			
			return $config;
		}
		
		public static function loadSettings($file, $use_namespace = false)
		{ 
			//Check to see if file is there, otherwise return false
			if(file_exists($file))
			{
				if(Zend_Session::isStarted() && $use_namespace == true)
				{
					if (Zend_Session::namespaceIsset((self::DEFAULT_NAMESPACE)))
					{
						$config_namespace = (object)Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);
						if (isset ($config_namespace->config))
						{
							$system_config = (object)unserialize($config_namespace->config);	
						} else {
							$system_config = new Zend_Config_Xml($file, null, true);
							
							$system_config = self::parseSettings($system_config, $system_config->runmode);
							
							$config_namespace->config = serialize($system_config);
							$config_namespace->config_last_filetime = filectime( $file );
							$config_namespace->config_filepath = $file;
						}
					} else {
						$system_config = new Zend_Config_Xml($file, null, true);
						
						$system_config = self::parseSettings($system_config, $system_config->runmode);
						
						$config_namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
						$config_namespace->config = serialize($system_config);
						$config_namespace->config_last_filetime = filectime( $file );
						$config_namespace->config_filepath = $file;
					}
				} else {
					$system_config = new Zend_Config_Xml($file, null, true);
					$system_config = self::parseSettings($system_config, $system_config->runmode);
				}
			} else {
				$config = self::getDefaultConfig();
				
				$writer = new Zend_Config_Writer_Xml(array ("config" => $config, "filename"=> $file));
				$writer->write();
				$system_config = $config;
				
				if (Zend_Session::isStarted() && $use_namespace == TRUE)
				{
					if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
					{
						$config_namespace = Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);
						$config_namespace->config = serialize($system_config);
						$config_namespace->config_last_filetime = filectime( $file );
						$config_namespace->config_filepath = $file;
						
					} else {
						$config_namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
						$config_namespace->config = serialize($system_config);
						$config_namespace->config_last_filetime = filectime( $file );
						$config_namespace->config_filepath = $file;
					}
				} else {
//					$config_namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
//					$config_namespace->config = serialize($system_config);
					// do nothing. sessions aren't started, no point in trying to save to session namespace.
				}
			}
			$system_config = ($system_config->runmode->use == 'production')?$system_config->production:$system_config->dev;
			return $system_config;
		}
		/**
		 * Parses dynamic values and returns a static copy.
		 *
		 * @param Zend_Config $settings
		 * @param Zend_Config $runmode
		 * @return Zend_Config
		 */
		public static function parseSettings($settings, $runmode) {
			
			$settingsArray = $settings->toArray();
			
			foreach($settingsArray as $key => $node)
			{
				if ( is_array($node) ) {
					$settings->{$key} = self::parseSettings( $settings->{$key}, $runmode );
				} else {
					
					foreach ($runmode as $runmode_key => $runmode_value) {
						$settings->{$key} = str_replace( "%$runmode_key%", $runmode_value, $node);
					}
					
				}
			}
			return $settings;
		}
		/**
		 * Flush our settings from the session namespace.
		 *
		 */
		public static function flush() {
			if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE)) {
				Zend_Session::namespaceUnset( self::DEFAULT_NAMESPACE );
			}
		}
		/**
		 * Returns the cached version of our settings.
		 *
		 * @return stdObject
		 */
		public static function getSettingsCached()
		{
			//if the session is started else return false
			if(Zend_Session::isStarted())
			{
				//if namespace has config grab it else return false
				if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
				{
					$config_namespace = (object)Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);

					$last_filetime = $config_namespace->config_last_filetime;
					$last_filepath = $config_namespace->config_filepath;
					
					$current_filetime = filectime($last_filepath);
					
					if ($current_filetime == $last_filetime) {
						//if the namespace is set return it else return false
						if(isset($config_namespace->config))
						{
							$settings = (object)unserialize($config_namespace->config);
							$settings = ($settings->runmode->use == 'production')?$settings->production:$settings->dev;
							
							return $settings;
						} else {
							return false;
						}
					} else {
						
						if (isset($last_filepath)) {
							// file changed ...load from file
							self::flush();
							
							$settings = self::loadSettings($last_filepath, true);
//							$settings = ($settings->runmode->use == 'production')?$settings->production:$settings->dev;
							
							return $settings;
						} else {
							return false;
						}
					}
				} else {
					return false;
				}
			} else {
				$settings = self::loadSettings(APPLICATION_PATH . '/settings/environment/settings.xml', false);
				return $settings;
			}
		}
		/**
		 * Saves our settings to disk.
		 *
		 * @param object $config
		 * @param string $file
		 */
		public static function saveSettings($config, $file)
		{
			$writer = new Zend_Config_Writer_Xml(array ("config" => $config, "filename"=> $file));
			$writer->write();
		}
		
		/**
		 * This script collects all data and settings for this installation, and
		 * performs a full backup ...saving the collected data into a .tar.gz file.
		 * 
		 * Windows users, please note: 'gzip', 'tar' and the 'mysql' binary locations 
		 * must be located in one of the folders found in the OS environment 
		 * 'PATH' variable.
		 * 
		 * @todo Detect paths to mysqldump, tar, and gzip programs - [DONE]
		 * @todo Export MySQL database data to files - [DONE]
		 * @todo Export SQLite database data to files - [DONE]
		 * @todo Export settings.xml - [DONE]
		 * @todo Collect exports, Tape ARchive it, compress (gz). - [DONE]
		 * @todo Register backup transaction somewhere.
		 * 
		 * @param int $timeLimitSeconds The max execution time allowed for this method.
		 */
		public static function systemBackup($timeLimitSeconds = 60) {
			set_time_limit($timeLimitSeconds);
			$log = '';
			$execStart = Debug::microTime();
		
			// ...Backup MySQL database
			$ds = DIRECTORY_SEPARATOR;
			$rootDirName = date('Y-m-d-H-m-s');
			$rootDirPath = realpath('../backup');
			$publicDir = realpath('./');
			$rootDir = $rootDirPath . $ds . $rootDirName;
			if (!file_exists($rootDir)) mkdir($rootDir);
			
			$settings = Zre_Config::getSettingsCached();
			$dbhost = $settings->db->hostname;
			$dbuser = $settings->db->username;
			$dbpass = $settings->db->password;
			$dbname = $settings->db->database;
			
			$dbtablePrepend = $settings->db->table_name_prepend;
			
			$backupFile = $rootDir . $ds . $dbname . ".sql.gz";
			
			$gzipBinary = null;
			$mysqlBinary = null;
			$tarBinary = null;
			
			switch(PHP_OS) {
				case 'WIN32': //no break statement.
				case 'WINNT': //no break statement.
				case 'Windows': //no break statement.
					
					// ...Try to find binary location(s)
					$paths = $_SERVER['PATH'];
					$paths = explode(';', $paths);
					
					// ...Additional common locations.
					$paths[] = realpath('C:\\Program Files'); 
					
					if (isset($_SERVER['MYSQL_HOME'])) {
						$mysqlBinary = realpath($_SERVER['MYSQL_HOME']) . $ds . 'mysqldump.exe';
						
					} else {
						
						foreach($paths as $path) {
							if (file_exists($path . $ds . 'mysqldump.exe')) {
								$mysqlBinary = realpath($path) . $ds . 'mysqldump.exe';
								break;
							}
						}
					}
					
					foreach($paths as $path) {
						
						if (file_exists($path . $ds . 'gzip.exe')) {
							$gzipBinary = realpath($path) . $ds . 'gzip.exe';
						}
						
						if (file_exists($path . $ds . 'tar.exe')) {
							$tarBinary = realpath($path) . $ds . 'tar.exe';
						} elseif (file_exists($path . $ds . 'GnuWin32' . $ds . 'bin' . $ds . 'tar.exe')) {
							$tarBinary = realpath($path) . $ds . 'GnuWin32' . $ds . 'bin' . $ds . 'tar.exe';
						}
					}
					
					// Check to make sure necessary binaries are present, else, end.
					if (!empty($mysqlBinary) && !empty($gzipBinary) && !empty($tarBinary)) {
						$command = "$mysqlBinary --host=$dbhost --user=$dbuser" . (!empty($dbpass) ? " --password=$dbpass" : '') . " $dbname | $gzipBinary > \"$backupFile\"";
					} else {
						throw new Zend_Exception('Could not location the mysqldump and/or gzip binaries.');
						exit;
					}
					
					break;
				default:
					$mysqlBinary = 'mysqldump';
					$gzipBinary = 'gzip';
					$tarBinary = 'tar';
					
					$command = "$mysqlBinary --host=$dbhost --user=$dbuser" . (!empty($dbpass) ? " --password=$dbpass" : '') . " $dbname | $gzipBinary > \"$backupFile\"";
					break;
			}
			
			
			$ret = '';
			$out = '';
			
			$log = "<pre>";
			$log .= "Backing up to $rootDir\n";
			system($command, $ret);
			
			$log .= "\nExecuted Mysql dump. Exit code '$ret'\n";
			
			
			// ... Backup SQLite file(s)
			$sqliteFolder = realpath('../application/settings/database/sqlite');
			$sqliteFiles = Zre_File::ls($sqliteFolder);
			$validFileExtensions = array('.sqlite', '.sq3');
			
			foreach($sqliteFiles as $file) {
				if ($file[0] != '.' && in_array(substr($file, -4), $validFileExtensions) ) {
					$src = $sqliteFolder . $ds . $file;
					$dest = $rootDir . $ds . $file;
					$wasCopied = false;
					
					try {
						@$wasCopied = copy($src, $dest);
					} catch (Exception $e) {
						$wasCopied = false;
					}
					
					if ($wasCopied == true) {
						$log .= "\nCopied $file";
					} else {
						$log .= "\n*ERROR* - could not copy '$src'";
					}
				}
					
			}
			
			// ... Backup settings.xml
			$settingsDir = realpath('../application/settings/environment');
			$settingsXml = 'settings.xml';
			
			$wasCopied = false;
			try {
				@$wasCopied = copy($settingsDir . $ds . $settingsXml, $rootDir . $ds . $settingsXml);
			} catch (Exception $e) {
				$wasCopied = false;
			}
			
			if ($wasCopied == true) {
				$log .= "\nCopied $settingsXml";
			} else {
				$log .= "\n*ERROR* - could not copy '" . $settingsDir . $ds . $settingsXml . "'";
			}
			
			// ... Tape ARchive the entire folder
			$command = '"' . $tarBinary . '" --create --file="' . $rootDirName . '.tar" "' . str_replace('\\', '/', '../backup/' . $rootDirName . '"');
			system($command, $ret);
			
			if ($ret == 0) {
				$log .= "\nTar execution complete. Exit code '$ret'";
			} else {
				$log .= "\nTar execution of '$rootDir' failed. Exit code '$ret'\nThe command was:\n$command";
			}
			
			// ... GZIP the folder content
			$command = $gzipBinary . ' "' . $rootDirName . '.tar"';
			system($command, $ret);
			if ($ret == 0) {
				$log .= "\nGzip compressed backup directory. Exit code '$ret'";
			} else {
				$log .= "\nGzip of '$rootDir' failed. Exit code '$ret'\nThe command was:\n$command";
			}
			
			// ...Move the archive to the backup dir.
			$command = 'move "' . $rootDirName . '.tar.gz" "' . $rootDirPath . '"';
			system($command, $ret);
			if ($ret == 0) {
				$log .= "\nMoved compressed archive to backup directory. Exit code '$ret'";
			} else {
				$log .= "\nCould not move '$rootDirName'.tar.gz - Exit code '$ret'\nThe command was:\n$command";
			}

			// ...Clean up.
			$cleanupFiles = array(
				$rootDir
			);
			
			foreach($cleanupFiles as $file) {
				switch(PHP_OS) {
					case 'WIN32': //no break statement.
					case 'WINNT': //no break statement.
					case 'Windows': //no break statement.
						$command = 'rmdir /S /Q "' . $file . '"';
						break;
					default:
						$command = 'rm -fR "' . $file . '"';
						break;
				}
				
				system($command, $ret);
				if ($ret == 0) {
					$log .= "\nDeleted '$file'\n";
				} else {
					$log .= "\nDeletion failed. *ERROR*\nFile: '$file'\nCommand: $command";
				}
			}
			$execEnd = Debug::microTime();
			$execTotalTime = $execEnd - $execStart;
			
			$log .= "\n\n*DONE* - execution time $execTotalTime ms";
			$log .= "</pre>";
			
			return $log;
		}
		
		public static function downloadBackup($backupId) {
			$dir = Zre_File::ls('../backup');
			$fileToDownload = '';
			
			foreach($dir as $file) {
				$fileExt = substr($file, -7, 7);
				$fileId = substr($file, 0, -7);
				
				if ($file[0] != '.' && 
					$fileExt == '.tar.gz' && 
					$fileId == $backupId) {
					
					$fileToDownload = $file;
					break;
				}
			}
			
			// Set headers
		    header("Cache-Control: public");
		    header("Content-Description: File Download");
		    header("Content-Disposition: attachment; filename=$fileToDownload");
		    header("Content-Type: application/x-gzip");
		    header("Content-Transfer-Encoding: binary");
		    
		    readfile("../backup/" . $fileToDownload);
		}
		
		public static function deleteBackup($backupId) {
			$return = null;
			if (is_string($backupId)) {
				$backupId = array($backupId);
			} elseif (!is_array($backupId)) {
				$return = null;
				exit;
			}
			
			$currentListing = Zre_File::ls('../backup');
			
			foreach($backupId as $id) {
				$id = trim($id);
				
				if ($id[0] != '.' &&
					in_array($id . '.tar.gz', $currentListing)) {
					
					$delete = Zre_File::delete('../backup/' . $id . '.tar.gz');
				}
			}
			
			$return = Zre_File::ls('../backup');
			foreach($return as $i => $file) {
				if ($file[0] == '.') unset($return[$i]);
			}
			return $return;
		}
	}
?>