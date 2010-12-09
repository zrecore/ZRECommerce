<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Ui
 * @subpackage Ui
 * @category Ui
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Ui_Form_Login - Login form.
 *
 */
class Zre_Ui_Form_Login extends Zend_Form
{
	const DEFAULT_NAMESPACE = 'ZRE_UI_FORM_LOGIN';
	
	public $max_login_attempts = 3;

	public function __construct($options = null, $max_login_attempts = 3)
	{
		parent::__construct($options);
		$this->setMethod( Zend_Form::METHOD_POST );
		
		$settings = Zre_Config::getSettingsCached();
		$t = Zend_Registry::get('Zend_Translate');
		
		$user_name = new Zend_Form_Element_Text('name');
		$user_name->setName('name');
		$user_name->setLabel('User');
		$user_name->setRequired(true);
		$user_name->setValidators(array(
			new Zend_Validate_NotEmpty(),
			new Zend_Validate_Alnum()
		));
		
		$password = new Zend_Form_Element_Password('password');
		$password->setName('password');
		$password->setLabel('Password');
		$password->setRequired(true);
		
		
		$password->setValidators(array(
			new Zend_Validate_NotEmpty()
		));
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setName('submit');
		$submit->setLabel('Login');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$this->max_login_attempts = $max_login_attempts;
		
		if (!Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE) ) 
		{
			$namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
			$namespace->login_attempts = $this->max_login_attempts;
			
		} else {
			$namespace = (object)Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);
		}
		$login_attempts = $namespace->login_attempts;
		
		if ($login_attempts > 1 )
		{
			$this->addElements( array($user_name, $password, $is_submitted, $submit) );
		} else {
			$captcha_field = new Zend_Form_Element_Captcha('captcha_field', array(
			  'label' => $t->_( $settings->site->captcha->label ),
			  'captcha' => array(
			  	'captcha' 	=> 'Image',
			  	'wordLen' 	=> (int) $settings->site->captcha->wordLen,
			  	'timeout' 	=> (int) $settings->site->captcha->timeout,
			  	'font' 		=> (string) $settings->site->captcha->font,
			  	'fontSize' 	=> (int) $settings->site->captcha->fontSize,
			  	'height'	=> (int) $settings->site->captcha->height,
			  	'imgDir' 	=> (string) $settings->site->captcha->imgDir,
			  	'imgURL' 	=> (string) $settings->site->captcha->imgUrl,
			    )
			));
			$captcha_field->addValidator( new Zend_Validate_NotEmpty(), true);
			$this->addElements( array($user_name, $password, $captcha_field, $is_submitted, $submit) );
		}
	}
	
	public static function deductLoginAttemptCount()
	{
		if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
		{
			$namespace = (object)Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);
			$login_attempts = $namespace->login_attempts;
			
			$login_attempts -= 1;
			if ($login_attempts < -1) $login_attempts = -1;
			
			Zend_Session::namespaceUnset(self::DEFAULT_NAMESPACE);
			$new_namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
			$new_namespace->login_attempts = $login_attempts;
			
		} else {
			return false;
		}
	}
	
	public function getLoginAttemptCount()
	{
		if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
		{
			$namespace = (object)Zend_Session::namespaceGet(self::DEFAULT_NAMESPACE);
			return $namespace->login_attempts;
		} else {
			return false;
		}
	}
	
	public function resetLoginAttemptCount()
	{
		if (Zend_Session::namespaceIsset(self::DEFAULT_NAMESPACE))
		{
			Zend_Session::namespaceUnset(self::DEFAULT_NAMESPACE);
			$new_namespace = new Zend_Session_Namespace(self::DEFAULT_NAMESPACE);
			$new_namespace->login_attempts = $this->max_login_attempts;
			
			return true;
		} else {
			return false;
		}
		
	}
	
}
?>