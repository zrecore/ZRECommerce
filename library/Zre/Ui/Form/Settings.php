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
 * Zre_Ui_Form_Settings - Settings edit form.
 *
 */
class Zre_Ui_Form_Settings 
{
	/**
	 * The form object
	 *
	 * @var Zend_Form
	 */
    protected $form;
    
    /**
     * Exceptions to required fields in the forms
     *
     * @var array
     */
    protected $requirementExceptions;
    
    const SALT = 'lI8s6h2%jXdjfk$l';
    
    /**
     * Form creation function
     * 
     * @param object $object  :: Object who's properties will be used to create form
     * @param boolean $topForm :: Set this to true when not a subform
     *              
     * @return null
     */
    public function __construct($object, $topForm = false) 
    {
        
        $this->requirementExceptions = $this->getRequirementExceptions();
        
        if ($topForm) {
            
            $this->form = new Zend_Form();
            
        } else {

            $this->form = new Zend_Form_SubForm();

        }

        $this->generateFormFieldsFromObject($object);
        
        if ($topForm) {
			/**
			 * @todo Retrieve salt from settings file or something.s
			 */
        	$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
        	$is_submitted->setValue(1);
        	
	        $this->form->setAction('/admin/index/settings/')
	        		   ->addElement( $is_submitted )
	                   ->setMethod( Zend_Form::METHOD_POST );
	                   
			$form_hash = new Zend_Form_Element_Hash( 'anti_csrf', array('salt'=>self::SALT));
			$form_hash->setRequired(true);
//	        $this->form->addElement( $form_hash, 'antic_xs');
	        $this->form->addElement('submit', 'Submit');
	        
        }
        
    }
    
    /**
     * Returns array of exceptions to required fields in forms
     */
    protected function getRequirementExceptions() {
        $exceptions = array('ups', 'usps', 'fedex');
        return $exceptions;
    }
    
    /**
     * Returns the form object
     * 
     * @return Zend_Form
     */
    public function getFormObject()
    {
        return $this->form;
    }
    
    /**
     * Iterates through the object and creates the form fields
     * Recursively calls this class to create sub forms for nested objects
     * 
     * @param object $object  :: Object to iterate over
     * @return null
     */
    protected function generateFormFieldsFromObject($object)
    {
        //Object iteration not working right, cast to array
        $array = (array) $object;
        foreach($array as $key=>$value) {
            
            if ($key == '@attributes') {
                continue;
            }

            if (is_object($value) && $value) {

                $subFormClass = new Zre_Ui_Form_Settings($value);
                $subForm = $subFormClass->getFormObject();
                $subForm->setLegend($key);
                $this->form->addSubForm($subForm, $key);

            } else {

                $formElement = new Zend_Form_Element_Text($key);
                $formElement->setLabel($key)
                            ->setValue($value);
                if(!in_array($key, $this->requirementExceptions)) {
                    $formElement->setRequired(true);
                }
                $this->form->addElement($formElement);
                
            }
            
        }
        
    }
    
}