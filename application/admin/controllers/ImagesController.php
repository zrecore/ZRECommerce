<?php

/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage MVC
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ImagesController - CRUD interface to our image records.
 * 
 * @author
 * @version 
 */
class Admin_ImagesController extends Zre_Controller_Crud_Json_Action {

    public function init() {
	// ...Set up our dataset object.
	$this->_dataset = new Zre_Dataset_Images();

	$this->_presetCreateFields = array(
	    'date_submitted' => new Zend_Db_Expr('NOW()'),
	    'date_modified' => new Zend_Db_Expr('NOW()')
	);
    }

    public function preDispatch() {
	$zend_auth = Zend_Auth::getInstance();
	$zend_auth->setStorage(new Zend_Auth_Storage_Session());
	$settings = Zre_Config::getSettingsCached();

	if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
	    $this->_redirect('https://' . $settings->site->url . '/admin/');
	}

	// All pages here require a valid login. Kick out if invalid.

	if (!$zend_auth->hasIdentity()) {
	    $this->_redirect('/admin/login');
	}

	$this->_helper->layout->disableLayout();
    }

    public function indexAction() {

    }

    public function renderAction() {

	$this->_helper->layout->disableLayout();
	$this->_helper->viewRenderer->setNoRender();

	$this->getResponse()->setHeader('Content-type', 'image/png');

	$request = $this->getRequest();

	try {
	    $id = $request->getParam('image_id');
	    $image = $this->_dataset->read($id);

	    if ($image->count() > 0) {

		$image_file = $image->current()->file;

		$filename = BASE_PATH . '/public/images/' . basename($image_file);

		if (file_exists($filename)) {
		    $type = pathinfo($filename, PATHINFO_EXTENSION);

		    switch ($type) {
			case 'jpg':
			case 'jpeg': // Break statement ommitted on purpose.

			    $img_ptr = imagecreatefromjpeg($filename);
			    imagejpeg($img_ptr);
			    break;

			case 'png':

			    $img_ptr = imagecreatefrompng($filename);
			    imagepng($img_ptr);

			    break;

			case 'gif':
			    $img_ptr = imagecreatefromgif($filename);
			    imagegif($img_ptr);
			    break;
			default:
			    throw new Exception('Unsupported image type');
		    }
		} else {
		    throw new Exception('Image file not found.');
		}
	    } else {
		throw new Exception('Image record not found.');
	    }
	} catch (Exception $e) {
	    // ... Just send back the dummy image;
	    Debug::log((string) $e);
	    $img_ptr = imagecreatefrompng(BASE_PATH . '/public/images/dummy.png');
	    imagepng($img_ptr);
	}
    }

    public function uploadAction() {

	/**
	 * Keep getting an error? Make sure either the php finfo class exists
	 * in your php installation, or mime_magic.magicfile is set in php.ini
	 */
	try {
	    $destDir = BASE_PATH . '/public/images';
	    $adapter = new Zend_File_Transfer_Adapter_Http();
	    $adapter->setDestination($destDir);

	    $mimeTypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif');

	    // ...Checking the file mime info requires extra libraries.
	    //
	    // @todo conditionally add IsImage validator if required dependencies
	    // are met

	    if ($adapter->receive() && $adapter->isValid() && in_array($adapter->getMimeType(), $mimeTypes)) {
		$targetFile = '/images/' . $adapter->getFileName();
		$reply =  str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);

		$dataset = new Zre_Dataset_Images();
		$dataset->create(array(
		    'file' => basename($adapter->getFileName()),
		    'date_submitted' => new Zend_Db_Expr('NOW()'),
		    'date_modified' => new Zend_Db_Expr('NOW()')
		));

		$this->_redirect('/admin/#images-tab');
	    } else {
		throw new Exception('Invalid file upload');
	    }
	} catch (Exception $e) {
	    $this->_redirect( '/admin/#images-tab?error=' . urlencode($e->getMessage()) );
	}
    }

    public function jsonDeleteAction() {
	try {
	    $key = $this->getRequest()->getParam('key');

	    $image = $this->_dataset->read($key);
	    if ($image->count() > 0) {

		$file = $image->current()->file;

                $result = Zre_File::delete(BASE_PATH . '/public/images/' . $file);

		parent::jsonDeleteAction();
	    } else {
		throw new Exception('Invalid ID specified.');
	    }
	} catch (Exception $e) {
	    $reply = array(
		'result' => 'error',
		'data' => (string) $e
	    );

	    $this->_helper->json($reply);
	}
    }

}