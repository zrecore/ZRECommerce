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
 * Zre_Ui_Form_Article - Article edit form.
 *
 */
class Zre_Ui_Form_Article extends Zend_Dojo_Form
{
	const SALT = 'lI8s6h2%jXdjfk$l';
	public function __construct($options=null, $returnUrl='')
	{
		parent::__construct($options);
		// Set our form properties
		$this->setMethod( Zend_Dojo_Form::METHOD_POST );
		
		// Set our hidden elements
		$id = new Zend_Form_Element_Hidden('id');
		$node_id = new Zend_Form_Element_Hidden('node_id');
		$container_id = new Zend_Form_Element_Hidden('container_id');
		
		$is_submitted = new Zend_Form_Element_Hidden('is_submitted');
		$is_submitted->setValue(1);
		
		$hidden_elements = array($id, $node_id, $is_submitted, $container_id);
		
		$form_hash = new Zend_Form_Element_Hash('anti_csrf', array('salt'=>self::SALT));
		$form_hash->setRequired(true);
		
		foreach($hidden_elements as $element) {
			$element->removeDecorator('Label');
			$element->removeDecorator('DtDdWrapper');
		}
		
		// Create our standard elements
		$use_zre_plugins = new Zend_Dojo_Form_Element_RadioButton('use_zre_plugins');
		
		$use_zre_plugins->addMultiOption('yes', 'Yes');
		$use_zre_plugins->addMultiOption('no', 'No');
		$use_zre_plugins->setLabel('Enable plugins:');
		$use_zre_plugins->setRequired(true);
		
		$published = new Zend_Dojo_Form_Element_RadioButton('published');
		$published->addMultiOption('yes', 'Yes');
		$published->addMultiOption('no', 'No');
		$published->setLabel('Published:');
		$published->setRequired(true);
		
		$image = new Zend_Dojo_Form_Element_TextBox('image');
		$image->setLabel('Image');
		$image->setDescription('The small image to display next to the article description. (120x120 pixels)');
		
		$resource = new Zend_Dojo_Form_Element_ComboBox('resource');
		/**
		 * Load list of resource types from an sqlite file. See trac item #7
		 */
		$articleResourceTypes = new Zre_Dataset_Article_Resource_Type();
		
		$data = $articleResourceTypes->read();
		
		if ( $articleResourceTypes->read() ) {
			
			$resourceTypes = array();
			foreach ($data as $index => $resourceType ) {
				$resourceTypes[$resourceType['type']] = $resourceType['type'];
			}
			
		} else {
			$resourceTypes = array(
				'article'		=> 'article',
				'news'	 		=> 'news',
				'newsletter'	=> 'newsletter',
				'latest'		=> 'latest',
				'announcement'	=> 'announcement',
				'homepage'		=> 'homepage'
			);
		}
		
		$resource->addMultiOptions( $resourceTypes );
		$resource->setLabel('Type:');
		$resource->setRequired(true);
		
		$save = new Zend_Dojo_Form_Element_SubmitButton('Save');
		$exit = new Zend_Dojo_Form_Element_Button('Exit');
		$exit->setAttrib('onclick', 'JavaScript: void(window.location = "'.$returnUrl.'");');
		
		// Our save and exit buttons need to align within a 'toolbar' display group.
		$save->removeDecorator('DtDdWrapper');
		$exit->removeDecorator('DtDdWrapper');
		
		$published->getDecorator('Label')->setOption(  'tag', 'div')->setOptions( array('class'=>'float-box-left') );
		$published->getDecorator('HtmlTag')->setOption('tag', 'span')->setOptions( array('class'=>'float-box-left') );
		
		$image->getDecorator('Label')->setOption(  'tag', 'div')->setOptions( array('class'=>'float-box-left') );
		$image->getDecorator('HtmlTag')->setOption('tag', 'span')->setOptions( array('class'=>'float-box-left') );
		
		$resource->getDecorator('Label')->setOption(  'tag', 'div')->setOptions( array('class'=>'float-box-left') );
		$resource->getDecorator('HtmlTag')->setOption('tag', 'span')->setOptions( array('class'=>'float-box-left') );
		
		$use_zre_plugins->getDecorator('Label')->setOption(  'tag', 'div')->setOptions( array('class'=>'float-box-left') );
		$use_zre_plugins->getDecorator('HtmlTag')->setOption('tag', 'span')->setOptions( array('class'=>'float-box-left') );
		
		$save->addDecorator(  new Zend_Form_Decorator_HtmlTag(			array('tag'=>'div', 'class'=>'float-box-right')) );
		$exit->addDecorator(  new Zend_Form_Decorator_HtmlTag(			array('tag'=>'div', 'class'=>'float-box-right')) );
		$use_zre_plugins->addDecorator( new Zend_Form_Decorator_HtmlTag(array('tag'=>'div', 'class'=>'float-box-left')) );
		$resource->addDecorator( new Zend_Form_Decorator_HtmlTag(array('tag'=>'div', 'class'=>'float-box-left')) );
//		$image->addDecorator( new Zend_Form_Decorator_HtmlTag(array('tag'=>'div', 'class'=>'float-box-left')) );
		
		// ...Continue creating the rest of our elements
		
		$title = new Zend_Dojo_Form_Element_TextBox('title');
		$title->setLabel('Title:');
		$title->setDescription('The article title. Must be 8 to 128 characters long.');
		$title->setRequired(true);
		$title->addValidator( new Zend_Validate_NotEmpty() );
		$title->addValidator( new Zend_Validate_Alnum(true) );
		
		$description = new Zend_Dojo_Form_Element_Textarea('description');
		$description->setLabel('Description:');
		$description->setDescription('A short description about this article. Must be 8 to 256 characters long.');
		$description->setRequired(true);
		$description->addValidator( new Zend_Validate_NotEmpty() );
		$description->addValidator( new Zend_Validate_StringLength(8, 256));
		
		$content = new Zend_Dojo_Form_Element_Editor('content');
		$content->setLabel('Content:');
		$content->setDescription('The article content that will be displayed.');
		
		// Add our elements to the form
		$elements = $hidden_elements;
		
		$elements[] = $title;
		$elements[] = $description;
		$elements[] = $content;
		$elements[] = $use_zre_plugins;
		$elements[] = $published;
		$elements[] = $image;
		$elements[] = $resource;
		$elements[] = $save;
		$elements[] = $exit;
		
		$this->addElements( $elements );
		
		$this->addDisplayGroup(array(
			'title',
			'description',
			'content',
			'image'
		), 'article', array('legend'=>''));
		
		$article = $this->getDisplayGroup('article');
		$article->setDecorators(array(
			'FormElements',
			'Fieldset',
			array( 'HtmlTag', array('tag'=>'div', 'style'=>'width: 100%; margin: auto; positon: relative;') )
		));
		
		$this->addDisplayGroup(array(
			'resource',
			'published',
			'use_zre_plugins',
			'Save',
			'Exit'
		), 'update_bar', array('legend'=>''));
		
		$update_bar = $this->getDisplayGroup('update_bar');
		$update_bar->setDecorators(array(
			'FormElements',
			'Fieldset',
			array( 'HtmlTag', array('tag'=>'div', 'class'=>'tool-bar') )
		));
		
		// Enable this form's dojo mojo :)
		Zend_Dojo::enableForm($this);
	}
}
?>