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
 * Zre_Ui_Form_Product - Product edit form.
 *
 */
class Zre_Ui_Form_Product extends Zend_Dojo_Form 
{
	const SALT = '4fs5gf5jL5lo55qR';
	public function __construct($options=null, $returnUrl='')
	{
		parent::__construct($options);
		$this->setMethod(Zend_Dojo_Form::METHOD_POST);
		
		$hidden_values = array();
		$form_values = array();
		
		$id = new Zend_Form_Element_Hidden('id');
		$container_id = new Zend_Form_Element_Hidden('container_id');
//		$tax_id = new Zend_Form_Element_Hidden('tax_id');
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$hidden_values = array($id, $is_submitted, $container_id);
		
		foreach ($hidden_values as $hidden)
		{
			$hidden->removeDecorator('HtmlTag');
			$hidden->removeDecorator('Label');
		}
		
		if (!empty($hidden_values)) 
		{
			$this->addElements($hidden_values);
		}
		
		$title = new Zend_Dojo_Form_Element_TextBox('title');
		$title->setRequired(true);
		$title->setLabel('Title');
		$title->addValidators( array(
			new Zend_Validate_NotEmpty(),
			new Zend_Validate_StringLength(0, 128)
		) );
		
		$description = new Zend_Dojo_Form_Element_Textarea('description');
		$description->setRequired(true);
		$description->setLabel('Description');
		$description->addValidators(array(
			new Zend_Validate_NotEmpty(),
			new Zend_Validate_StringLength(0, 256)
		));
		
		$price = new Zend_Dojo_Form_Element_NumberSpinner('price');
		$price->setRequired(true);
		$price->setLabel('Price');
		$price->addValidators(array(
			new Zend_Validate_NotEmpty(),
//			new Zend_Validate_Regex('/[0-9]*\.[0-9][0-9]/'),
			new Zend_Validate_GreaterThan(-1)
		));
		
		$allotment = new Zend_Dojo_Form_Element_NumberSpinner('allotment');
		$allotment->setRequired(true);
		$allotment->setLabel('Allotment');
		$allotment->addValidators(array(
			new Zend_Validate_NotEmpty(),
			new Zend_Validate_Digits(),
			new Zend_Validate_GreaterThan(-1)
		));
		
		$pending = new Zend_Dojo_Form_Element_NumberSpinner('pending');
		$pending->setRequired(true);
		$pending->setLabel('Pending');
		$pending->addValidators(array(
			new Zend_Validate_NotEmpty(),
			new Zend_Validate_Digits(),
			new Zend_Validate_GreaterThan(-1)
		));
		
		$sold = new Zend_Dojo_Form_Element_NumberSpinner('sold');
		$sold->setRequired(true);
		$sold->setLabel('Sold');
		$sold->addValidators(array(
			new Zend_Validate_NotEmpty(),
			new Zend_Validate_Digits(),
			new Zend_Validate_GreaterThan(-1)
		));
		
		$save = new Zend_Dojo_Form_Element_SubmitButton('Save');
		$exit = new Zend_Dojo_Form_Element_Button('Exit');
		$exit->setAttrib('onclick', 'JavaScript: void(window.location = "'.$returnUrl.'");');
		
		// Our save and exit buttons need to align within a 'toolbar' display group.
		$save->removeDecorator('DtDdWrapper');
		$exit->removeDecorator('DtDdWrapper');
		$save->addDecorator(  new Zend_Form_Decorator_HtmlTag(			array('tag'=>'div', 'class'=>'float-box-right')) );
		$exit->addDecorator(  new Zend_Form_Decorator_HtmlTag(			array('tag'=>'div', 'class'=>'float-box-right')) );
		
		
		$published = new Zend_Dojo_Form_Element_RadioButton('published');
		$published->addMultiOption('yes', 'Yes');
		$published->addMultiOption('no', 'No');
		$published->setLabel('Published:');
		$published->setRequired(true);
		
		$published->getDecorator('Label')->setOption(  'tag', 'div')->setOptions( array('class'=>'float-box-left') );
		$published->getDecorator('HtmlTag')->setOption('tag', 'span')->setOptions( array('class'=>'float-box-left') );
		
		$image = new Zend_Dojo_Form_Element_TextBox('image');
		$image->setLabel('Image');
		$image->setDescription('The small image to display next to the article description. (120x120 pixels)');
		
		$form_values = array(
			$title,
			$description,
			$price,
			$allotment,
			$pending,
			$sold,
			$published,
			$image,
			$save,
			$exit
		);
		
		$this->addElements( $form_values );
		
		$this->addDisplayGroup(array(
			'title',
			'description',
			'price',
			'image'
		), 'productproperties', array('legend'=>''));
		
		$productproperties = $this->getDisplayGroup('productproperties');
		$productproperties->setDecorators(array(
			'FormElements',
			'Fieldset',
			array( 'HtmlTag', array('tag'=>'div', 'style'=>'width: 100%; margin: auto; positon: relative;') )
		));
		
		$this->addDisplayGroup(array(
			'allotment',
			'pending',
			'sold'
		), 'allotments', array('legend'=>''));
		
		$this->addDisplayGroup(array(
			'published',
			'Save',
			'Exit'
		), 'update_bar', array('legend'=>''));
		
		$update_bar = $this->getDisplayGroup('update_bar');
		$update_bar->setDecorators(array(
			'FormElements',
			'Fieldset',
			array( 'HtmlTag', array('tag'=>'div', 'class'=>'tool-bar') )
		));
		Zend_Dojo::enableForm($this);
	}
}
?>