<?php 

/**
 * ACL Model - Abstracts ACL XML config for Crud Operations
 *
 * @author Jack Forrest
 */
class Zre_Dataset_AclConfig {
	private static $_acl;
	
	private static $_resources;
	private static $_roles;
	/**
	 * Creates a new Role, Resource or Permission
	 *
	 * @param string $type
	 * @param array $options
	 */
	public static function create($type, $options) {
		$acl = $this->getAcl();
		
		switch ($type) {
			case 'role':
				$acl->addRole( new Zend_Acl_Role($options['name']), $options['inherits'] );
				break;
			case 'resource':
				$acl->add( new Zend_Acl_Resource($options['name']), $options['inherits'] );
				break;
			case 'permission':
				if ( $type == 'allow') {
					if ( $options['resource'] == 'all' ) {
						$acl->allow_all($options['role']);
					}
					$acl->allow( $options['role'], $options['resource'], $options['permissions'] );
				} elseif( $options['type'] == 'deny' ) {
					if ( $options['resource'] == 'all' ) {
						$this->allow_all($options['role']);
					}
					$acl->deny( $options['role'], $options['resource'], $options['permissions'] );
				}
				break;
			default:
				return false;
				break;
		}
		
		self::setAcl( $acl );
		return true;
	}
	
	/**
	 * Gets the internal ACL object
	 *
	 * @return Zend_Acl
	 */
	public static function getAcl() {
		if (!isset(self::$_acl)) {
			self::$_acl = Zre_Acl::getInstance();
		}
		return self::$_acl;
	}
	/**
	 * Set the internal ACL object
	 *
	 * @param Zend_Acl $acl
	 */
	public static function setAcl( $acl ) {
		if ( $acl instanceof Zend_Acl ) {
			self::$_acl = $acl;
		}
	}
	public static function save() {
		Zre_Acl::setInstance( self::getAcl() );
		Zre_Acl::save();
	}
	
	/**
	 * Removes roles, resources and permissions from the ACL
	 *
	 * @param string $type role, resource or permission
	 * @param array $options details for the item
	 */
	public function delete($type, $options) {
		$acl = self::getAcl();
		
		switch ( $type ) {
			case 'role':
				$acl->removeRole($options['name']);
				return true;
				
				break;
			case 'resource':
				$acl->removeResource($options['name']);
				return true;
				
				break;
			case 'permission':
				if($options['type'] == 'allow') {
					if($options['resource'] == 'all') {
						$acl->deny_all($options['role']);
					} else {
						$acl->allow($options['role'],
							$options['resource'], $options['permissions']);
					}
				} elseif($options['type'] == 'deny') {
					if($options['resource'] == 'all') {
						$acl->allow_all($options['role']);
					} else {
						$acl->deny($options['role'],
							$options['resource'], $options['permissions']);
					}// end if $options ['resource']
				}// end if permission type
				break;
			default:
				return false;
				break;
		}
		
		self::setAcl( $acl );
		return true;
	}
}
?>