<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Auth
 * @category Auth
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Auth_User - CRUD implementation of user accounts, and then some.
 *
 */
class Zre_Auth_User
{
	/**
	 * Adds a user to the database using the values submitted by Zre_Ui_Form_Register.
	 * 
	 * @param array $params;
	 */
	public static function addUser($params)
	{
		$values_users = array();
		$values_users_string = "";
		
		$values_users_profile = array();
		$values_users_profile_string = "";
		
		$settings = Zre_Config::getSettingsCached();
		
		
		$db = new Zre_Db_Adapter_Mysql();
		
		$sql_users_string = "INSERT INTO ".((string)$settings->db->table_name_prepend).			"users (";
		$sql_users_profile_string = "INSERT INTO ".((string)$settings->db->table_name_prepend).	"users_profile (";
		
		$users_pstatement = null;
		$users_profile_pstatement = null;
		
		$user_param_count = 0;
		$user_profile_param_count = 0;
		/**
		 * Build our INSERT query...
		 */
		foreach($params as $key => $value)
		{
			switch($key)
			{
//				case 'role': // This column is set to 'guest' by default when no value is specified in the query.
				case 'name':
				case 'password':
				case 'first_name':
				case 'last_name':
				case 'email':
				case 'date_of_birth':
				case 'country':
				case 'city':
				case 'state_province':
				case 'zipcode':
				case 'telephone_primary':
				case 'telephone_secondary':
					switch ($key)
					{
						case 'name':
						case 'password':
							if (!isset($values_users[$key]))
							{
								$values_users[$key] = $value;
								$values_users_string .= ":$key".($user_param_count < 1?",":"");
								$sql_users_string .= 	"$key".($user_param_count < 1?",":"");
								$user_param_count++;
							}
							break;
						default:
							if (!isset($values_users_profile[$key]))
							{
								$values_users_profile[$key] = $value;
								$values_users_profile_string .= ":$key".($user_profile_param_count < 9?",":"");
								$sql_users_profile_string .= 	"$key".($user_profile_param_count < 9?",":"");
								$user_profile_param_count++;
							}
							break;
					}
					
					break;
				default:
					break;
			}
		}
		
		$sql_users_string .= ") VALUES ($values_users_string)";
		$sql_users_profile_string .= ", user_id) VALUES ($values_users_profile_string, :user_id)";
		
//		echo "<pre>$sql_users_string\n\n$sql_users_profile_string</pre>"; exit;
		
		$users_pstatement = $db->prepare($sql_users_string);
		$users_profile_pstatement = $db->prepare($sql_users_profile_string);
		
		$db->beginTransaction();
		
		try
		{
			foreach($values_users as $key => $value)
			{
				if ($key == 'password') $value = md5($value);
				$users_pstatement->bindValue(":$key", $value);
			}
			
			foreach($values_users_profile as $key1 => $value1)
			{
				$users_profile_pstatement->bindValue(":$key1", $value1);
			}
			
			if($users_pstatement->execute())
			{
				$user_id = $db->lastInsertId();
				
				$users_profile_pstatement->bindValue(':user_id', $user_id);
				
				if (!$users_profile_pstatement->execute())
				{
					throw new Zend_Exception('Could not add user profile.', 1);
				}
			} else {
				throw new Zend_Exception('Could not add user.', 1);
			}
			
		} catch (Exception $e) {
			$db->rollBack();
			unset($users_pstatement);
			unset($users_profile_pstatement);
			return $e;
		}
		
		$db->commit();
		unset($users_pstatement);
		unset($users_profile_pstatement);
		return true;
	}
	/**
	 * Update a user property
	 * @param array $params The key/value pairs to check for new values.
	 * @return true|false|Exception Returns true on success. Returns false, or an Exception object on failure.
	 */
	public function updateUser($params)
	{
		$values_users = array();
		$values_users_string = "";
		
		$values_users_profile = array();
		$values_users_profile_string = "";
		
		$settings = Zre_Config::getSettingsCached();
		
		
		$db = new Zre_Db_Adapter_Mysql();
		
		$sql_users_string = "UPDATE ".((string)$settings->db->table_name_prepend).			"users SET ";
		$sql_users_profile_string = "UPDATE ".((string)$settings->db->table_name_prepend).	"users_profile SET ";
		
		$users_pstatement = null;
		$users_profile_pstatement = null;
		
		$user_param_count = 0;
		$user_profile_param_count = 0;
		/**
		 * Build our INSERT query...
		 * 
		 * @return true Returns true on success.
		 * @return false|Exception Returns false, or an Exception object on failure.
		 */
		$user_id = (int)$params['id'];
		foreach($params as $key => $value)
		{
			switch($key)
			{
				case 'role':
				case 'name':
				case 'password':
				case 'first_name':
				case 'last_name':
				case 'email':
				case 'date_of_birth':
				case 'country':
				case 'city':
				case 'state_province':
				case 'zipcode':
				case 'telephone_primary':
				case 'telephone_secondary':
					switch ($key)
					{
						case 'name': // Don't allow a user name change, as these must remain unique.
						case 'password':
							if (!isset($values_users[$key]) && !empty($value)) //Only update if a value is specified.
							{
								$values_users[$key] = $value;
//								$values_users_string .= ":$key".($user_param_count < 1?",":"");
								$sql_users_string .= 	"$key=:$key".($user_param_count < 1?",":"");
								$user_param_count++;
							}
							break;
						default:
							if (!isset($values_users_profile[$key]) && !empty($value))
							{
								$values_users_profile[$key] = $value;
//								$values_users_profile_string .= ":$key".($user_profile_param_count < 9?",":"");
								$sql_users_profile_string .= 	"$key=:$key".($user_profile_param_count < 9?",":"");
								$user_profile_param_count++;
							}
							break;
					}
					
					break;
				default:
					break;
			}
		}
		
		if (strripos($sql_users_string, ',') == strlen($sql_users_string)-1) $sql_users_string = substr($sql_users_string, 0, strlen($sql_users_string)-1);
		if (strripos($sql_users_profile_string, ',') == strlen($sql_users_profile_string)-1) $sql_users_profile_string = substr($sql_users_profile_string, 0, strlen($sql_users_profile_string)-1);
		
		if (count($values_users) > 0)
		{
			$sql_users_string .= " WHERE id=:user_id";
		} else {
			
			$sql_users_string = null;
		}
		
		if (count($values_users_profile) > 0)
		{
			$sql_users_profile_string .= " WHERE user_id=:user_id";
		} else {
			
			$sql_users_profile_string = null;
		}
		
//		echo "<pre>$sql_users_string\n\n$sql_users_profile_string</pre>";

		$db->beginTransaction();
		try
		{
			if (count($values_users) > 0)
			{
				$users_pstatement = $db->prepare($sql_users_string);
				foreach($values_users as $key => $value)
				{
					if ($key == 'password') $value = md5($value);
					$users_pstatement->bindValue(":$key", $value);
				}
				$users_pstatement->bindParam(":user_id", $user_id, PDO::PARAM_INT);
				
				if(!$users_pstatement->execute())
				{
					throw new Zend_Exception('Could not update user.', 1);
				}
			}
			
			if (count($values_users_profile) > 0)
			{
				$users_profile_pstatement = $db->prepare($sql_users_profile_string);
				foreach($values_users_profile as $key1 => $value1)
				{
					$users_profile_pstatement->bindValue(":$key1", $value1);
					
				}
				$users_profile_pstatement->bindValue(':user_id', $user_id);
				if (!$users_profile_pstatement->execute())
				{
					throw new Zend_Exception('Could not update user profile.', 1);
				}
			}
			
		} catch (Exception $e) {
			
			$db->rollBack();
			
			unset($users_pstatement);
			unset($users_profile_pstatement);
			
			return $e;
		}

		$db->commit();
		unset($users_pstatement);
		unset($users_profile_pstatement);
		return true;
	}
}
?>