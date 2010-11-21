<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Boot
 * @subpackage Boot
 * @category Boot
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

require_once realpath(dirname(__FILE__) . '/../application/bootstrap.php');

Bootstrap::setupPaths();
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/settings/application.ini'
);

$application->bootstrap();
$application->run();