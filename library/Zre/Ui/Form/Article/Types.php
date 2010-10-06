<?php
class Zre_Ui_Form_Article_Types extends Zend_Form {
	public function __construct( $options = null ) {
		parent::__construct( $options );
		$typeComboBox = new Zend_Form_Element_Select('types');
		$types = Zre_Dataset_Article_Resource_Type::read();
		$typeComboBoxOptions = array();
		
		foreach($types as $type) {
			$typeComboBoxOptions[$type['id']] = $type['type'];
		}
		
		$typeComboBox->setAttribs(array(
						'size' => 20,
						'style' => 'width:100%' ))
					->setMultiOptions( $typeComboBoxOptions );
		$typeComboBox->setLabel('Available resource types:');
		$typeComboBox->setAttrib('onchange', 
			"JavaScript: void(document.getElementById('id').value = this.value);
						 void(document.getElementById('type').value = this.options[this.selectedIndex].text);");
					
		$id = new Zend_Form_Element_Hidden('id');
		$id->removeDecorator('DtDdWrapper');
		
		$type = new Zend_Form_Element_Text('type');
		$type->setLabel('Resource type name:');
		
		$createType = new Zend_Form_Element_Button('New');
		$createType->removeDecorator('DtDdWrapper');
		
		$updateType = new Zend_Form_Element_Button('Update');
		$updateType->removeDecorator('DtDdWrapper');
		
		$deleteType = new Zend_Form_Element_Button('Delete');
		$deleteType->removeDecorator('DtDdWrapper');
		
		$isSubmitted = new Zend_Form_Element_Hidden('is_submitted');
		$isSubmitted->removeDecorator('DtDdWrapper');
		$isSubmitted->setValue(1);
		
		$submitAction = new Zend_Form_Element_Hidden('submit_action');
		$submitAction->removeDecorator('DtDdWrapper');
		
		$this->addElements(array(
			$type,
			$typeComboBox,
			$createType,
			$updateType,
			$deleteType,
			$isSubmitted,
			$id,
			$submitAction
		));
		
	}
}
?>