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
 * Zre_Ui_Form_Dialog_YesNoAbort - A yes/no dialog.
 *
 */
class Zre_Ui_Form_Dialog_YesNoAbort extends Zend_Form 
{
	public function __construct($options = null, $label = 'Continue?')
	{
		parent::__construct($options);
		$this->setMethod(Zend_Form::METHOD_POST);
		$yes_no_abort = new Zend_Form_Element_Radio('yes_no_abort');
		$yes_no_abort->addMultiOptions(array(
			'yes'=>'Yes',
			'no'=>'No',
			'abort'=>'Abort'
		));
		
		$yes_no_abort->setLabel($label);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Next');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$this->addElements( array($yes_no_abort, $is_submitted, $submit) );
	}
}
?>