<?php
class Zre_Dataset_Model_Logs extends Zend_Db_Table_Abstract {
	protected $_name = 'logs';
	protected $_primary = 'id';
	
	public function __construct($config = array())
	{
		$settings = Zre_Config::getSettingsCached();
    	
    	
		$this->_name = $settings->db->table_name_prepend . $this->_name;
		$this->_primary = 'id';
		$this->setDefaultAdapter( Zre_Db_Mysql::getInstance() );
		
		parent::__construct($config);
	}
}