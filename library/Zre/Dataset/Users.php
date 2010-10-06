<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Dataset
 * @subpackage Dataset
 * @category Dataset
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Datasets_Users - Provides CRUD implementation of data sets related to
 * user accounts.
 *
 */
class Zre_Dataset_Users extends Zre_Dataset_Abstract 
{
	/**
	 * The "Users" database table model.
	 *
	 * @var Zre_Dataset_Model_Users
	 */
	private static $_modelUsers;
	/**
	 * The "Users Profile" database table model.
	 *
	 * @var Zre_Dataset_Model_UsersProfile
	 */
	private static $_modelUsersProfile;
	
	/**
	 * Creates a new user and user profile using the specified $data
	 *
	 * @param array $data
	 * @return boolean
	 */
	public static function getUsersTable()
	{
		if (!isset(self::$_modelUsers))
		{
			self::$_modelUsers = new Zre_Dataset_Model_Users();
		}
		
		return self::$_modelUsers;
	}
	/**
	 * Gets the User Profile Table datamodel
	 *
	 * @return Zre_Dataset_Model_UsersProfile
	 */
	public static function getUsersProfileTable()
	{
		if (!isset(self::$_modelUsersProfile))
		{
			self::$_modelUsersProfile = new Zre_Dataset_Model_UsersProfile();
		}
		
		return self::$_modelUsersProfile;
	}
	/**
	 * Create a new user.
	 *
	 * @param array $data
	 * @return boolean;
	 */
	public static function create( $data )
	{
		$values_users = array();
		$values_users_profile = array();

		$usersTable = self::getUsersTable();
		$usersProfileTable = self::getUsersProfileTable();

		foreach($data as $key => $value)
		{
			switch($key)
			{
				case 'role': case 'name': case 'password':
				case 'first_name': case 'last_name': case 'email':
				case 'date_of_birth': case 'country': case 'city':
				case 'state_province': case 'zipcode': case 'telephone_primary':
				case 'telephone_secondary': // Break statements omitted intentionally.
					
					switch ($key)
					{
						case 'name': // Don't allow a user name change, as these must remain unique.
						case 'password':
							if (!isset($values_users[$key]) && !empty($value)) //Only update if a value is specified.
							{
								$values_users[$key] = $value;
							}
							break;
						default:
							if (!isset($values_users_profile[$key]) && !empty($value))
							{
								$values_users_profile[$key] = $value;
							}
							break;
					}
					
					break;
				default:
					break;
			}
		}
		
		$userId = $usersTable->insert( $values_users );
		$values_users_profile['user_id'] = $userId;
		
		$usersProfileTable->insert( $values_users_profile );
		
		return true;
	}
	/**
	 * Retrieve user data associated with the specified $userId
	 *
	 * @param int $userId
	 * @return array
	 */
	public static function read( $userId )
	{
    	$data = array();
    	
   		$id = $userId;
   		$usersTable = self::getUsersTable();
		$usersProfileTable = self::getUsersProfileTable();
		
		$selectUser = $usersTable
						->select()->where('user_id = ?', $id);
		
		$selectUserProfile = $usersProfileTable
						->select()->where('user_id = ?', $userId);
						
		$userRow = 			$usersTable->fetchAll( $selectUser );
		$userProfileRow = 	$usersProfileTable->fetchAll( $selectUserProfile );
		
		$usersArray = $userRow->toArray();
		$usersProfileArray = $userProfileRow->toArray();
		
		$data = array_merge( $usersArray[0], $usersProfileArray[0] ); 

    	return $data;
	}
	/**
	 * Update a user and user profile associated with the specified $userId
	 *
	 * @param int $userId
	 * @param array $data
	 * @return boolean
	 */
	public static function update( $userId, $data )
	{
		$values_users = array();
		$values_users_profile = array();

		$usersTable = self::getUsersTable();
		$usersProfileTable = self::getUsersProfileTable();

		$id 	= (int)$userId;

		$userValues = Zre_Dataset::filterColumns( $data, Zre_Dataset_Users::getUsersTable() );
		$userProfileValues = Zre_Dataset::filterColumns( $data, Zre_Dataset_Users::getUsersProfileTable() );

		if (!$userValues['password']) {
			unset($userValues['password']);
		} else {
			$userValues['password'] = md5($userValues['password']);
		}
				
		$data = array_merge($userValues, $userProfileValues);
		
		foreach($data as $key => $value)
		{
			switch($key)
			{
				case 'role': case 'name': case 'password':
				case 'first_name': case 'last_name': case 'email':
				case 'date_of_birth': case 'country': case 'city':
				case 'state_province': case 'zipcode': case 'telephone_primary':
				case 'telephone_secondary': // Break statements omitted intentionally.
					
					switch ($key)
					{
						case 'name': // Don't allow a user name change, as these must remain unique.
						case 'password':
							if (!isset($values_users[$key]) && !empty($value)) //Only update if a value is specified.
							{
								$values_users[$key] = $value;
							}
							break;
						default:
							if (!isset($values_users_profile[$key]) && !empty($value))
							{
								$values_users_profile[$key] = $value;
							}
							break;
					}
					
					break;
				default:
					break;
			}
		}
		
		$usersTable->update( $values_users, $usersTable->getAdapter()->quoteInto('user_id = ?', $id) );
		$usersProfileTable->update( $values_users_profile, $usersProfileTable->getAdapter()->quoteInto('user_id = ?', $userId) );
		
		return true;
	}
	/**
	 * Delete a user (and user profile) associated with the specified $userId
	 *
	 * @param int $userId
	 * @return array
	 */
	public static function delete( $userId )
	{	
		$usersTable = self::getUsersTable();
		$usersTable->delete( $usersTable->getAdapter()->quoteInto('user_id = ?', $userId) );
	}
	
	public static function getUserProfile( $userId ) {
		$userProfileTable = self::getUsersProfileTable();
		
		$select = $userProfileTable->select()->where('user_id = ?', $userId)->limit(1);
		$result = $userProfileTable->fetchAll( $select );
		
		$data = $result->toArray();
		
		return $data[0];
	}
}