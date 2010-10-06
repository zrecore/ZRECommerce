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
 * Zre_Ui_Form_Article_Categories - Article edit form.
 *
 */
class Zre_Ui_Form_Article_Categories extends Zend_Dojo_Form
{
	public function __construct($options=null, $returnUrl='')
	{
		$t = Zend_Registry::get('Zend_Translate');
		
		parent::__construct($options);
		// Set our form properties
		$this->setMethod( Zend_Dojo_Form::METHOD_POST );
		$this->setAttribs( 
			array(	'id' => 'frmCategories',
					'action' => '/admin/articles/categories/' ));
		
		
		// Set our hidden elements
		$id = new Zend_Form_Element_Hidden('id');
		$parent_id = new Zend_Form_Element_Hidden('parent_id');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$hidden_elements = array($id, $parent_id, $is_submitted);

		foreach($hidden_elements as $element) {
			$element->removeDecorator('Label');
			$element->removeDecorator('DtDdWrapper');
		}
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title');
		
		$description = new Zend_Form_Element_Text('description');
		$description->setLabel('Description');
		
		$elements = array();
		$elements = $hidden_elements;
		$elements[] = $title;
		$elements[] = $description;
		
		$newButton = new Zend_Form_Element_Button('New');
		$newButton->removeDecorator('DtDdWrapper');
		$newButton->setAttribs(array(
			'onclick' => "JavaScript: 
			
				var frmCategories = document.getElementById('frmCategories');
				frmCategories.attributes.getNamedItem('action').value = '/admin/articles/category.add/';
				frmCategories.submit();
			" ));
		
		
		$saveButton = new Zend_Form_Element_Submit('Save');
		$saveButton->removeDecorator('DtDdWrapper');
		
		$deleteButton = new Zend_Form_Element_Button('Delete');
		$deleteButton->removeDecorator('DtDdWrapper');
		$deleteButton->setAttribs(array(
			'onclick' => "JavaScript: 
			var dlgAllowRemove = confirm('" . $t->_('confirm_delete_category') . "');
			if (dlgAllowRemove) {
				window.location = ('/admin/articles/category.remove/id/' + document.getElementById('id').value);
			}" ));
		
		$elements[] = $newButton;
		$elements[] = $saveButton;
		$elements[] = $deleteButton;
		
		$this->addElements( $elements );
	}
}
?>