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
 * Zre_Ui_Form_Dialog_OkCancel - An Ok/Cancel dialog.
 *
 */
class Zre_Ui_Form_Dialog_OkCancel extends Zend_Dojo_Form 
{
	public function __construct($options = null, $label = 'Are you sure?')
	{
		parent::__construct($options);
		$this->setMethod(Zend_Dojo_Form::METHOD_POST);
		$ok_cancel = new Zend_Dojo_Form_Element_RadioButton('ok_cancel');
		$ok_cancel->addMultiOptions(array(
			'ok'=>'Ok',
			'cancel'=>'Cancel'
		));
		
		$ok_cancel->setLabel($label);
		
		$submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
		$submit->setValue('Next');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$this->addElements( array($ok_cancel, $is_submitted, $submit) );
		Zend_Dojo::enableForm($this);
	}
}
?>