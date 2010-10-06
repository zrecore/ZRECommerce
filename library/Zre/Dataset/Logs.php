<?php
class Zre_Dataset_Logs extends Data_Set_Abstract {
	public static function init($modelName=null, $primaryIdColumn=null) {
		$modelName = 'Zre_Dataset_Model_Logs';
		$primaryIdColumn = 'id';
		parent::init($modelName, $primaryIdColumn);
	}
}