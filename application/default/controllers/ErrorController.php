<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Default
 * @subpackage Default_Error
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ErrorController - The default error controller class
 * 
 */
class ErrorController extends Zend_Controller_Action
{

    /**
     * This action handles  
     *    - Application errors
     *    - Errors in the controller chain arising from missing 
     *      controller classes and/or action methods
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found                
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->title = 'HTTP/1.1 404 Not Found';
                break;
            default:
                // application error; display error page, but don't change                
                // status code
                $this->view->title = 'Application Error';
                break;
        }
        
        
        $error_output = '
        The following was logged:
        Env:
        ' . APPLICATION_ENV . '
        Exception:
        ' . (string) $errors->exception . '
        
        Session:
        ' . print_r($_SESSION, true) . '
        ';
        Debug::log($error_output);
        $this->view->message = "<a href=\"/\">Back to home.</a><br /><br />\n<div style=\"background-color: #ffffff; color: #000000;\">". str_replace("\n", "\n<br />",$errors->exception) . "</div>\n<br /><br /><a href=\"/\">Back to home.</a>";
        
        Zre_Registry_Session::set('selectedMenuItem', '');
		Zre_Registry_Session::save();
    }
}