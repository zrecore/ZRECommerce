<?php
class Zre_Dataset_UsersEx extends Data_Set_Abstract
{
	protected $_modelName = 'Zre_Dataset_Model_Users';

	public function getProfiles($data = null, $options = null, $table_name_prepend = null) {
		$pre = '';
		if (isset($table_name_prepend)) $pre = $table_name_prepend;
		
		$columnOptions = array(
			'setIntegrityCheck' => false,
			'from' => array(
				'name' => array($pre . 'users', $pre . 'users_profile'),
				'cols' => array(
					$pre . 'users.user_id',
					$pre . 'users.name',
					$pre . 'users.creation_date', 
					$pre . 'users_profile.*'
				)
			),
			'leftJoinUsing' => array(
				'name' => $pre . 'users_profile',
				'join' => 'user_id'
			)
		);
		if (isset($options)) {
			$allOptions = array_merge($options, $columnOptions);
			$options = $allOptions;
		} else {
			$options = $columnOptions;
		}
		return parent::listAll($data,$options);
		
	}
	/**
	 * Create a new user and user_profile record.
	 * @param array $data
	 */
	public function createProfile($data) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$usersTable = $this->getModel();
		$usersProfileTable = new Zre_Dataset_Model_UsersProfile();
		
		$userData = Data::filterColumns($data, $usersTable);
		$userId = $usersTable->insert( $userData );
		
		$data['user_id'] = $userId;
		if (isset($data['user_profile_id'])) unset($data['user_profile_id']);
		if (isset($data['password']) && !$data['password'] instanceof Zend_Db_Expr) $data['password'] = new Zend_Db_Expr("MD5(" . $db->quote($data['password']) . ")");
		
		$userProfileData = Data::filterColumns($data, $usersProfileTable);
		$userProfileId = $usersProfileTable->insert( $userProfileData );
		
		return $userId;
	}
}