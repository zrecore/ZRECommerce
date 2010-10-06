<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Acl
 * @category Acl
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Acl - Access control lists instantiation, and validation, class.
 *
 */
class Zre_Acl
{
	private static $_acl;
	
	private static $_roles;
	private static $_resources;
	private static $_denyRules;
	private static $_allowRules;
	
	/**
	 * Make ACL Instance
	 * 
	 * Loads default.acl and creats an ACL object from it's parameters
	 */
	private static function makeInstance()
	{
		$aclFromDisk = self::load();
		if (isset($aclFromDisk)) {
			
			return $aclFromDisk;
		} else {
			$acl = new Zend_Acl();
			
			/**
			 * Default roles.
			 */
			$roles = array(
				array('name' => 'guest'),
				array('name' => 'staff', 'parents' => array('guest')),
				array('name' => 'editor', 'parents' => array('staff')),
				array('name' => 'administrator')
			);
			
		
			/**
			 * Default resources.
			 */
			$resources = array(
				array('name' => 'administration'),
				array('name' => 'administration.index', 'parent' => 'administration'),
				array('name' => 'articles', 'parent' => 'administration'),
				array('name' => 'backup', 'parent' => 'administration'),
				array('name' => 'error', 'parent' => 'administration'),
				array('name' => 'products', 'parent' => 'administration'),
				array('name' => 'users', 'parent' => 'administration'),
				array('name' => 'logs', 'parent' => 'administration'),
				array('name' => 'orders', 'parent' => 'administration'),
				array('name' => 'acl', 'parent' => 'administration'),
				array('name' => 'searchSettings', 'parent' => 'administration'),
				array('name' => 'plugins', 'parent' => 'administration'),
				array('name' => 'index')
			);
			
			/**
			 * Default deny privileges.
			 */
			$denyRules = array(
				array('name' => 'guest'),
			);
			/**
			 * Default allow privileges
			 */
			$allowRules = array(
				array('name' => 'staff', 'resources' => array('articles'), 'privileges' => array('view', 'update', 'new')),
				array('name' => 'editor', 'resources' => array('articles'), 'privileges' => array('publish', 'archive', 'remove')),
				array('name' => 'administrator')
			);
			
			foreach ( $roles as $role ) {
				$aclRole = new Zend_Acl_Role($role['name']);
				
				if (isset($role['parents'])) {
					$acl->addRole( $aclRole, $role['parents']);
				} else {
					$acl->addRole( $aclRole );
				}
			}
			foreach ( $resources as $resource ) {
				$aclResource = new Zend_Acl_Resource( $resource['name'] );
				
				if (isset($resource['parent'])) {
					$acl->add( $aclResource, $resource['parent'] );
				} else {
					$acl->add( $aclResource );
				}
			}
			foreach($denyRules as $deny) {
				$acl->deny( $deny['name'], 
							(isset($deny['resources']) ? $deny['resources'] : null ),
							(isset($deny['privileges']) ? $deny['privileges'] : null )
						);
			}
			foreach($allowRules as $allow) {
				$acl->deny( $allow['name'], 
							(isset($allow['resources']) ? $allow['resources'] : null ),
							(isset($allow['privileges']) ? $allow['privileges'] : null )
						);
			}
			
			self::$_acl = $acl;
			self::$_roles = $roles;
			self::$_resources = $resources;
			self::$_denyRules = $denyRules;
			self::$_allowRules = $allowRules;
			
			echo self::save();
			
			return self::$_acl;
		}
	}
	/**
	 * Save our ACL
	 *
	 * @return boolean
	 */
	public static function save() {
		$aclRole = new Zre_Dataset_Acl_Role();
		$aclResource = new Zre_Dataset_Acl_Resource();
		$aclDeny = new Zre_Dataset_Acl_Deny();
		$aclAllow = new Zre_Dataset_Acl_Allow();
		
		$aclRole->flush();
		$aclResource->flush();
		$aclDeny->flush();
		$aclAllow->flush();
		
		$roles = self::$_roles;
		$resources = self::$_resources;
		$denyRules = self::$_denyRules;
		$allowRules = self::$_allowRules;
		
		foreach($roles as $role) {
			
			if (is_array($role['parents'])) $role['parents'] = serialize($role['parents']);
			
			$aclRole->create( $role );
		}
//		echo "created roles...<br />";
		foreach($resources as $resource) {
			
			if (is_array($resource['parent'])) $resource['parent'] = serialize($resource['parent']);
			
			$aclResource->create( $resource );
		}
//		echo "created resources...<br />";
		foreach($denyRules as $deny) {
			
			if (is_array($deny['resources'])) $deny['resources'] = serialize($deny['resources']);
			if (is_array($deny['priviliges'])) $deny['privileges'] = serialize($deny['privileges']);
			
			$aclDeny->create( $deny );
		}
//		echo "created deny rules...<br />";
		foreach($allowRules as $allow) {
			
			if (is_array($allow['resources'])) $allow['resources'] = serialize($allow['resources']);
			if (is_array($allow['privileges'])) $allow['privileges'] = serialize($allow['privileges']);
			
			$aclAllow->create( $allow );
		}
		
//		echo "created allow rules...<br />Done."; exit;
//		return true;
	}
	/**
	 * Load our ACL. Returns null upon failure.
	 *
	 * @return Zend_Acl|null
	 */
	public static function load() {
		
		$acl = new Zend_Acl();
		
		$aclRole = new Zre_Dataset_Acl_Role();
		$aclResource = new Zre_Dataset_Acl_Resource();
		$aclDeny = new Zre_Dataset_Acl_Deny();
		$aclAllow = new Zre_Dataset_Acl_Allow();
		
		$roles = $aclRole->listAll(null, array('order' => 'acl_role_id ASC'));
		$resources = $aclResource->listAll(null, array('order' => 'acl_resource_id ASC'));
		$denyRules = $aclDeny->listAll(null, array('order' => 'acl_deny_id ASC'));
		$allowRules = $aclAllow->listAll(null, array('order' => 'acl_allow_id ASC'));
		
		if (count($roles) < 1 && count($resources) < 1 && count($denyRules) < 1 && count($allowRules) < 1) {
			return null;
		}
		
//		echo '<pre>' . print_r($roles, true) . '</pre>';
//		echo '<pre>' . print_r($resources, true) . '</pre>';
//		echo '<pre>' . print_r($denyRules, true) . '</pre>';
//		echo '<pre>' . print_r($allowRules, true) . '</pre>';
//		exit;
		foreach ( $roles as $role ) {
			$aclRole = new Zend_Acl_Role($role['name']);
			
			if (isset($role['parents']) && $role['parents'] != '') {
				$acl->addRole( $aclRole, unserialize($role['parents']));
			} else {
				$acl->addRole( $aclRole );
			}
		}

		foreach ( $resources as $resource ) {
			
			$aclResource = new Zend_Acl_Resource( $resource['name'] );
			
			if (isset($resource['parent']) && $resource['parent'] != '') {
				$acl->add( $aclResource, $resource['parent'] );
			} else {
				$acl->add( $aclResource );
			}
		}
		
		foreach($denyRules as $deny) {
			$acl->deny( $deny['name'], 
						(isset($deny['resources']) && $deny['resources'] != '' ? unserialize($deny['resources']) : null ),
						(isset($deny['privileges']) && $deny['privileges'] != '' ? unserialize($deny['privileges']) : null )
					);
		}
		
		foreach($allowRules as $allow) {
			$acl->allow( $allow['name'], 
						(isset($allow['resources']) && $allow['resources'] != '' ? unserialize($allow['resources']) : null ),
						(isset($allow['privileges']) && $allow['privileges'] != '' ? unserialize($allow['privileges']) : null )
					);
		}
		
		
		self::$_acl = $acl;
		return self::$_acl;
		
//		$settings = Zre_Config::getSettingsCached();
//		try {
//			$serialized = file_get_contents( (string)$settings->site->acl_file );
//			if (!empty($serialized)) {
//				$acl = unserialize( $serialized );
//				
//				if ($acl instanceof Zend_Acl ) {
//					self::$_acl = $acl;
//					return self::$_acl;
//				} else {
//					return null;
//				}
//			} else {
//				return null;
//			}
//		} catch (Exception $e) {
//			return null;
//		}
	}

	/**
	 * Returns an instance of the Zend_Acl object.
	 * @return Zend_Acl
	 */
	public function getInstance()
	{
		if (!isset(self::$_acl))
		{
			self::makeInstance();
		}
		return self::$_acl;
	}
	
	/**
	 * Sets the Zend_Acl instance
	 *
	 * @param Zend_Acl $acl
	 */
	public function setInstance( $acl ) {
		self::$_acl = $acl;
	}
	/**
	 * Checks to see if the current user is allowed the specified priviledge on 
	 * the specified resource.
	 *
	 * @param string $resource
	 * @param string $priviledge
	 * 
	 * @return boolean
	 */
	public static function isAllowed($resource, $priviledge) {
		$zreAcl = Zre_Acl::getInstance();
		$currentUserProfile = Zre_Registry_Session::get('CURRENT_USER_PROFILE');
		
		return $zreAcl->isAllowed($currentUserProfile['role'], $resource, $priviledge);
	}
}
?>