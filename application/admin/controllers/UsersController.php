<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Users
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * UsersController - User administration controller
 *
 */
class Admin_UsersController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() && 
			(Zre_Acl::isAllowed('users', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')) ) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=>'true'));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		
		Zre_Registry_Session::set('selectedMenuItem', 'Users');
		Zre_Registry_Session::save();
		$this->_helper->layout->disableLayout();
	}
	/**
	 * Default User administration page.
	 */
	public function indexAction()
	{
		// TODO Auto-generated UsersController::indexAction() default action
		$settings = Zre_Config::getSettingsCached();
		$pre = $settings->db->table_name_prepend;
		
		$t = Zend_Registry::get('Zend_Translate');
		$this->view->title = $t->_('User Profile');
		
		$users = new Zre_Dataset_UsersEx();
		$records = $users->getProfiles(null, null, $pre);
		
		$this->view->assign('records', $records);
	}
	/**
	 * Update user action.
	 *
	 */
	public function updateAction()
	{
		if (Zre_Acl::isAllowed('users', 'update')) {
			$settings = Zre_Config::getSettingsCached();
	    	
	    	
	    	$t = Zend_Registry::get('Zend_Translate');
	    	
	    	$this->view->assign('enable_dojo', 1);
	    	$this->view->title = $t->_('User Profile');
	    	
//			$right_column_entries = new Zre_Ui_Sidebar_Menu();
			$user_form = new Zre_Ui_Form_Register(null, $this->getRequest());
			
			$params = '';
			$params_array = array(
				'id' => $this->getRequest()->getParam('id'), 
				'start_index' => $this->getRequest()->getParam('start_index'), 
				'max_per_page' => $this->getRequest()->getParam('max_per_page')
			);
			
			// Grab our key/value POST vars, for use with our datagrid links.
			foreach($params_array as $key => $value)
			{
				$params .= '/'.$key . '/' . $value;
			}
	    	$url_params = $params;
	    	
			$user_form->setAction('/admin/users/update'.$params);
			$user_form->removeElement('captcha_field');
			$user_form->removeElement('submit');
			
			$select_role = new Zend_Dojo_Form_Element_RadioButton('role');
			$select_role->setLabel('Select role');
			$select_role->addMultiOptions(array(
				'guest' => 'Guest',
				'staff' => 'Staff',
				'editor' => 'Editor',
				'administrator' => 'Administrator'
			));
			
			$submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
			$submit->setLabel('Update');
			$user_id_element = new Zend_Form_Element_Hidden('user_id');
			
			$user_form->addElements(array($select_role, $user_id_element, $submit));
			$user_form->getElement('password')->setRequired(false);
			$user_form->getElement('retype_password')->setRequired(false);
			
			$is_submitted = $this->getRequest()->getParam('is_submitted');
			$params = $this->getRequest()->getParams();
			
			if ($is_submitted)
			{	
				$updateResult = Zre_Dataset_Users::update( $params['user_id'], $params );
				
				if ($updateResult === true)
				{
					$data = Zre_Dataset_Users::read( $params['user_id'] );
					$user_form->setDefaults( $data );
					
					$this->_redirect('/admin/users/index/start_index/'.$params_array['start_index'].'/max_per_page/'.$params_array['max_per_page'].'');
					
				} else {
					// Display an error
					$user_form->setDefaults( $params );
					$this->view->assign('content', '<div class="errors">'.$t->_('Error:').' '.$t->_('Invalid user name. Please try a different one.').'</div>');
					
				}
			} else {
				// Grab the selected user's profile info. Display it.
				$user_id = $this->getRequest()->getParam('id');
				
				$data = Zre_Dataset_users::read( $user_id );
				unset($data['password']);
				$user_form->setDefaults( $data );
	
			}
			$this->view->form = $user_form;
//			$this->view->assign('right_column', $right_column_entries->get());
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	/**
	 * Remove user action.
	 *
	 */
	public function removeAction()
	{
		if (Zre_Acl::isAllowed('users', 'remove')) {
			$settings = Zre_Config::getSettingsCached();
	    	
	    	
	    	$t = Zend_Registry::get('Zend_Translate');
	    	
	    	$this->view->title = $t->_('User Profile');
	    	
			$params = '';
			$params_array = array(
				'id' => $this->getRequest()->getParam('id'), 
				'start_index' => $this->getRequest()->getParam('start_index'), 
				'max_per_page' => $this->getRequest()->getParam('max_per_page')
			);
			
			foreach($params_array as $key => $value)
			{
				$params .= '/'.$key . '/' . $value;
			}
	    	
			if ($this->getRequest()->getParam('is_submitted'))
			{
				$id = $this->getRequest()->getParam('id');
				
				switch($this->getRequest()->getParam('yes_no_abort'))
				{
					case 'yes':
						$usersDataset = new Zre_Dataset_Users();
						$usersDataset->delete( $id );
						
						$this->view->assign('content', '<div><b>'.$t->_('Ok:').'</b>Deleted user. <a href="/admin/users/index/start_index/'.$params_array['start_index'].'/max_per_page/'.$params_array['max_per_page'].'">Back to user listing.</a></div>');
						break;
					case 'no':
						$this->view->assign('content', '<div><b>'.$t->_('Cancelled:').'</b> '.$t->_('No update was performed.').' <a href="/admin/users/index/start_index/'.$params_array['start_index'].'/max_per_page/'.$params_array['max_per_page'].'">'.$t->_('Continue').'</a></div>');
						break;
					default:
						$this->_redirect('/admin/users/index/start_index/'.$params_array['start_index'].'/max_per_page/'.$params_array['max_per_page'].'');
						break;
				}
				
			} else {
				// Need to confirm action to avoid accidental deletes.
				$form = new Zre_Ui_Form_Dialog_YesNoAbort();
				$this->view->form = $form;
			}
		} else {
			$this->_redirect('/admin/', array('exit'=>'true'));
		}
	}
	
	public function jsonListAction() {
		
		$request = $this->getRequest();
		$data = null;
		
		try {
			$t = Zend_Registry::get('Zend_Translate');
			$settings = Zre_Config::getSettingsCached();
			$pre = $settings->db->table_name_prepend;
			
			$users = new Zre_Dataset_UsersEx();
			
			$sort = $request->getParam('sort', 'name');
			$order = $request->getParam('order', 'ASC');
			
			$options = array(
				'order' => $sort . ' ' . $order
			);
			
			$records = $users->getProfiles(null, $options, $pre);
			
			$data = array(
				'result' => 'ok',
				'data' => $records
			);
			
		} catch (Exception $e) {
			$data = array(
				'result' => 'error',
				'data' => (string) $e
			);
			Debug::log((string) $e);
		}
		$this->_helper->json($data);
	}
	
	public function jsonCreateAction() {
		$request = $this->getRequest();
		$data = null;
		
		try {
			$users = new Zre_Dataset_UsersEx();
			$data = $users->createProfile($request->getParams());
		} catch (Exception $e) {
			Debug::log((string)$e);
		}
		
		$this->_helper->json($data);
	}
}