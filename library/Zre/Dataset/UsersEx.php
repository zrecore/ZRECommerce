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
			    'name' => array('u' => $pre . 'users'),
			    'cols' => array(
				'user_id',
				'name',
				'creation_date'
			    )
			),
			'leftJoin' => array(
			    'name' => array('uP' => $pre . 'users_profile'),
			    'cond' => 'uP.user_id = u.user_id',
			    'cols' => array(
				'user_profile_id',
				'email',
				'date_of_birth',
				'first_name',
				'last_name',
				'country',
				'state_province',
				'city',
				'zipcode',
				'telephone_primary',
				'telephone_secondary',
				'role'
			    )
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

	public function updateProfile($data, $user_id) {
		$db = Zend_Db_Table::getDefaultAdapter();

		$usersProfileTable = new Zre_Dataset_Model_UsersProfile();
		$userProfileData = Data::filterColumns($data, $usersProfileTable);
		$rowsAffected = $usersProfileTable->update( $userProfileData, $db->quoteInto('user_id = ?', $user_id) );
		
		return $rowsAffected;
	}
}