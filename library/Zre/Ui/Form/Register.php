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
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * Zre_Ui_Form_Register - User registration, and user account edit form.
 *
 */
class Zre_Ui_Form_Register extends Zend_Dojo_Form 
{
	const DEFAULT_NAMESPACE = 'Zre_Ui_Form_Register';
	
	public $max_register_attempts = 3;
	
	public function __construct($options = null, $request=null)
	{
		parent::__construct($options);
		$this->setMethod( Zend_Dojo_Form::METHOD_POST );
		
		$settings = Zre_Config::getSettingsCached();
		$t = Zend_Registry::get('Zend_Translate');
		
		$user_name = new Zend_Dojo_Form_Element_TextBox('name');
		$user_name->setName('name');
		$user_name->setLabel('User');
		$user_name->setRequired(true);
		$user_name->setValidators(array(
			new Zend_Validate_Alnum()
		));
		
		$password = new Zend_Dojo_Form_Element_PasswordTextBox('password');
		$password->setName('password');
		$password->setLabel('Password');
		$password->setRequired(true);
		$password->setValidators(array(
			new Zend_Validate_StringLength(8)
		));
		
		$retype_password = new Zend_Dojo_Form_Element_PasswordTextBox('retype_password');
		$retype_password->setName('retype_password');
		$retype_password->setLabel('Re-type Password');
		$retype_password->setRequired( true );
		
		
		
		if (isset($request))
		{
			$pass = $request->getParam('password');
			$identical = new Zend_Validate_Identical($request->getParam('password'));
			$identical->setMessage( 'Passwords do not match', Zend_Validate_Identical::NOT_SAME );
		} else {
			$identical = new Zend_Validate_NotEmpty();
		}
		
		$retype_password->setValidators(array(
			new Zend_Validate_StringLength(8),
			$identical
		));
		
		$first_name = new Zend_Dojo_Form_Element_TextBox('first_name');
		$first_name->setName('first_name');
		$first_name->setLabel('First Name');
		$first_name->setRequired(true);
		$first_name->setValidators(array(
			new Zend_Validate_Alnum(true),
			new Zend_Validate_StringLength(1, 128)
		));
		
		$last_name = new Zend_Dojo_Form_Element_TextBox('last_name');
		$last_name->setName('last_name');
		$last_name->setLabel('Last Name');
		$last_name->setRequired(true);
		$last_name->setValidators(array(
			new Zend_Validate_Alnum(true),
			new Zend_Validate_StringLength(0, 128)
		));
		
		$email = new Zend_Dojo_Form_Element_TextBox('email');
		$email->setName('email')
		->setLabel('E-Mail')
		->setRequired(true)
		->setValidators(array(
//			new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS, true)
			new Zend_Validate_EmailAddress()
		));
		
		$date_of_birth = new Zend_Dojo_Form_Element_DateTextBox('date_of_birth');
		$date_of_birth->setName('date_of_birth')
		->setLabel('Date of Birth')
		->setRequired(true)
		->setValidators(array(
			new Zend_Validate_Date(array('format' => 'yyyy-MM-dd'))
		));
		
		$country = new Zend_Dojo_Form_Element_TextBox('country');
		$country->setName('country')
		->setLabel('Country')
		->setRequired(true)
		->setValidators(array(
			new Zend_Validate_Alpha(true),
		));
		
		$state_province = new Zend_Dojo_Form_Element_TextBox('state_province');
		$state_province->setName('state_province')
		->setLabel('State or Province')
		->setRequired(true)
		->setValidators(array(
			new Zend_Validate_Alnum(true)
		));
		
		$city = new Zend_Dojo_Form_Element_TextBox('city');
		$city->setName('city')
		->setLabel('City')
		->setRequired(true)
		->setValidators(array(
			new Zend_Validate_Alnum(true)
		));
		
		$zipcode = new Zend_Dojo_Form_Element_TextBox('zipcode');
		$zipcode_regex = new Zend_Validate_Regex('/[0-9\-]/');
		$zipcode_regex->setMessage('Invalid zip code format, try 99999 or 99999-1234', Zend_Validate_Regex::NOT_MATCH);
		
		$zipcode->setName('zipcode')
		->setLabel('Zip Code')
		->setRequired(true)
		->setValidators(array(
			$zipcode_regex
		));
		
		$telephone_digits_only = new Zend_Validate_Digits();
		$telephone_digits_only->setMessage("Invalid phone number format. Try 7772228888 or 17772228888", Zend_Validate_Digits::NOT_DIGITS);
		$telephone_primary = new Zend_Dojo_Form_Element_TextBox('telephone_primary');
		$telephone_primary->setName('telephone_primary')
		->setDescription('Example: 7772228888, or 17772228888')
		->setLabel('Primary Telephone')
		->setRequired(true)
		->setValidators(array(
			$telephone_digits_only
		));
		
		$telephone_secondary = new Zend_Dojo_Form_Element_TextBox('telephone_secondary');
		$telephone_secondary->setName('telephone_secondary')
		->setDescription('Example: 7772228888, or 17772228888')
		->setLabel('Secondary Telephone')
		->setValidators(array(
			$telephone_digits_only
		));
		
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
		
		$submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
		$submit->setName('submit');
		$submit->setLabel('Register');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$this->addElements( array( 
			$user_name, 
			$password, 
			$retype_password,
			$first_name,
			$last_name,
			$email,
			$date_of_birth,
			$country,
			$state_province,
			$city,
			$zipcode,
			$telephone_primary,
			$telephone_secondary, 
			$captcha_field, 
			$submit, 
			$is_submitted ) );
			
		Zend_Dojo::enableForm( $this );
	}
}
?>