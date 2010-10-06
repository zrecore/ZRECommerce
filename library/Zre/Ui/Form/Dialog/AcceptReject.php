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
 * Zre_Ui_Form_Dialog_AcceptReject - An accept/reject dialog.
 *
 */
class Zre_Ui_Form_Dialog_AcceptReject extends Zend_Dojo_Form 
{
	public function __construct($options = null, $label = 'Do you accept?')
	{
		parent::__construct($options);
		$this->setMethod(Zend_Dojo_Form::METHOD_POST);
		$accept_reject = new Zend_Dojo_Form_Element_RadioButton('accept_reject');
		$accept_reject->addMultiOptions(array(
			'accept'=>'Accept',
			'reject'=>'Reject'
		));
		
		$accept_reject->setLabel($label);
		
		$submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
		$submit->setValue('Next');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$this->addElements( array($accept_reject, $is_submitted, $submit) );
		Zend_Dojo::enableForm($this);
	}
}
?>