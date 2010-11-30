<?php
class Zre_Dataset_Model_Images extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'images';
	protected $_primary = 'image_id';
	
	public function __construct($config = array())
	{
		$settings = Zre_Config::getSettingsCached();
    	
    	
		$this->_name = $settings->db->table_name_prepend . $this->_name;
		$this->setDefaultAdapter( Zre_Db_Mysql::getInstance() );
		
		parent::__construct($config);
	}

}