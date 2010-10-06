<?php 

/**
 * ACL Model - Abstracts ACL XML config for Crud Operations
 *
 * @author Jack Forrest
 */
class Zre_Acl_Model {
	protected $config;
	
	/**
	 * Loads config array from config XML
	 */
	public function __construct() {
		$xml = new Zend_Config_Xml('../../settings/acl/default.xml');
		$this->config = $xml->acl->toArray();
	}
	
	/**
	 * Creates a new Role, Resource or Permission
	 *
	 * @param array $options
	 */
	public function create(array $options) {
		$type = $options['type'];
		
		if($type == 'role') {
			$config['roles'][$options['name']] = null;
			if($options['inherits']) {
				$config['roles'][$options['name']] = array('inherits' => $options['inherits']);
			}
		} elseif($type == 'resource') {
			$config['resources'][$options['name']] = null;
			if($options['inherits']) {
				$config['resources'][$options['name']] = array('inherits' => $options['inherits']);
			}
		} elseif($type == 'permission') {
			$config['permissions'][$options['permtype']][] = array(
				'role' => $options['role'], 'resource' => $options['resource'], 'privelege' => $options['privelege'] );
		}
	}
	
	public function toJson() {
		return json_encode($this->config);
	}
	
	public function read(string $type = null) {
		if($type) {
			return $this->config[$type];
		} else {
			return $this->config;
		}
	}
	
	/**
	 * Removes an item by it's options
	 * <code>
	 * 	'type' => 'role'
	 *  'item' => 'guest'
	 * 
	 *  'type' => 'permission'
	 *  'permtype' => 'deny'
	 *  'item' => 1 // ignored in cases where there's only one item
	 * </code>
	 *
	 * @param array $options
	 */
	public function delete(array $options) {
		$type = $options['type'];
		$item = $options['item'];
		
		if($type == 'role') {
			unset($this->config['roles'][$item]);
			foreach($this->config['roles'] as $role=>$attrs) {
				if($attrs['inherits'] && $attrs['inherits'] == $item) {
					unset($this->config['roles'][$role]);
				}
			}
		} elseif($type == 'resource') {
			$parent = $this->getRequest()->getPost('parent');
			if($parent == '') {
				unset($this->config['resources'][$item]);
				foreach($this->config['resources'] as $resource=>$attrs) {
					if($attrs['inherits'] && $attrs['inherits'] == $item){
						unset($this->config['resources'][$resource]);
					}
				}
			} else {
				unset($this->config['resources'][$parent][$item]);
			}
		} elseif($type == 'permission') {
			$permtype = $options['permtype'];
			/**
			 * @todo Throw error for non int $item
			 */
			if(is_array($this->config['permissions'][$permtype])) {
				unset($this->config['permissions'][$permtype][$item]);
			} else {
				unset($this->config['permissions'][$permtype]);
			}
		}
	}
	
	public function getConfig() {
		return $this->config();
	}
}
?>