<?php

class CRM_Expertapplication_UserRole {
  
  protected $contacts_ids;
  
  public function __construct($activity_id) {
    $this->contact_ids = $this->retrieveTargetIdsByActivityId($activity_id);
  }
  
  /**
   * Retrieve assignee_id by activity_id
   *
   * @param int    $id  ID of the activity
   *
   * @return void
   *
   * @access public
   *
   */
  protected function retrieveTargetIdsByActivityId($activity_id) {
    $assigneeArray = array();
    if (!CRM_Utils_Rule::positiveInteger($activity_id)) {
      return $assigneeArray;
    }

    $activityContacts = CRM_Core_OptionGroup::values('activity_contacts', FALSE, FALSE, FALSE, NULL, 'name');
    $assigneeID = CRM_Utils_Array::key('Activity Targets', $activityContacts);

    $sql = "
SELECT     contact_id
FROM       civicrm_activity_contact
INNER JOIN civicrm_contact ON contact_id = civicrm_contact.id
WHERE      activity_id = %1
AND        record_type_id = $assigneeID
AND        civicrm_contact.is_deleted = 0
";
    $assignment = CRM_Core_DAO::executeQuery($sql, array(1 => array($activity_id, 'Integer')));
    while ($assignment->fetch()) {
      $assigneeArray[] = $assignment->contact_id;
    }

    return $assigneeArray;
  }
  
  public function process() {
    $config = CRM_Expertapplication_Config::singleton();
    if (!$config->getDrupalRole()) {
      CRM_Core_Session::setStatus('No user account created becasue an invalid role is set up', 'No user account created', 'error');
      return;
    }
    foreach($this->contact_ids as $cid) {
      //check if this user exist in drupal
      $drupal_uid = $this->createDrupalUser($cid);
      if ($drupal_uid) {
        //activate user
        $user = user_load($drupal_uid);
        $user->status = 1; //activate user
        user_save($user);  
          
        //assign role to drupal user
        $this->assignRoleToUser($drupal_uid, $config->getDrupalRole());
        //set the message
        try {
          $contact = civicrm_api3('Contact', 'getsingle', array('id' => $cid));
          CRM_Core_Session::setStatus('User account created for '.$contact['display_name'], 'User account created', 'success');
        } catch (Exception $e) {
          CRM_Core_Session::setStatus('User account created', 'User account created', 'success');
        }
      }
    }
  }
  
  protected function assignRoleToUser($uid, $role_id) {
    $user = user_load($uid);
    if (isset($user->roles[$role_id])) {
      return;
    }
    
    $roles = user_roles(TRUE);
    $role_name = $roles[$role_id];
    $user_roles = $user->roles;
    $user_roles[$role_id] = $role_name;
    user_save($user, array('roles' => $user_roles));
  }
  
  protected function createDrupalUser($contact_id) {
    $drupal_uid = $this->getDurpalUserId($contact_id);
    if ($drupal_uid !== false) {
      return $drupal_uid;
    }
    
    //create user in drupal
    //user the form api to create the user
    $form_state = form_state_defaults();
    try {
      $contact = civicrm_api3('Contact', 'getsingle', array('id' => $contact_id));
    } catch (Exception $e) {
      CRM_Core_Session::setStatus('No user account created because contact could not be found', 'No user account created', 'error');
    }
    
    try {
     $email = civicrm_api3('Email', 'getsingle', array('contact_id' => $contact_id, 'is_primary' => '1'));   
    } catch (Exception $ex) {
       CRM_Core_Session::setStatus('No user account created because contact does not have an e-mail address', 'No user account created', 'error');
    }
    
    $name = $email['email'];
    
    $form_state['input'] = array(
      'name' => $name,
      'mail' => $email['email'],
      'op' => 'Create new account',
      'notify' => true,
    );
    
    $pass = $this->randomPassword();
    $form_state['input']['pass'] = array('pass1'=>$pass,'pass2'=>$pass);
    
    $form_state['rebuild'] = FALSE;
    $form_state['programmed'] = TRUE;
    $form_state['complete form'] = FALSE;
    $form_state['method'] = 'post';
    $form_state['build_info']['args'] = array();
    /*
    * if we want to submit this form more than once in a process (e.g. create more than one user)
    * we must force it to validate each time for this form. Otherwise it will not validate
    * subsequent submissions and the manner in which the password is passed in will be invalid
    * */
    $form_state['must_validate'] = TRUE;
    $config = CRM_Core_Config::singleton();

    // we also need to redirect b......
    $config->inCiviCRM = TRUE;

    /*
     * We have created a duplicate of the drupal user_register_form function
     * just to create a default form so that we could set that an administrator
     * has created the account, rather the really role the user has
     */
    $form = $this->getUserRegisterForm($form_state);
    
    //process the form with standard drupal functionality
    $form_state['process_input'] = 1;
    $form_state['submitted'] = 1;
    $form['#array_parents'] = array();
    $form['#tree'] = FALSE;
    drupal_process_form('user_register_form', $form, $form_state);

    $config->inCiviCRM = FALSE;

    if (form_get_errors()) {
      CRM_Core_Session::setStatus('No user account created', 'No user account created', 'error');
      return FALSE;
    }
    $drupal_uid = $form_state['user']->uid;
    
    $ufmatch             = new CRM_Core_DAO_UFMatch();
    $ufmatch->domain_id  = CRM_Core_Config::domainID();
    $ufmatch->uf_id      = $drupal_uid;
    $ufmatch->contact_id = $contact_id;
    $ufmatch->uf_name    = $name;

    if (!$ufmatch->find(TRUE)) {
      $ufmatch->save();
    }
    
    return $drupal_uid;
  }
  
  protected function getUserRegisterForm(&$form_state) {
    global $user;

    $admin = 1;//user_access('administer users');

    // Pass access information to the submit handler. Running an access check
    // inside the submit function interferes with form processing and breaks
    // hook_form_alter().
    $form['administer_users'] = array(
      '#type' => 'value',
      '#value' => $admin,
    );

    // If we aren't admin but already logged on, go to the user page instead.
    if (!$admin && $user->uid) {
      drupal_goto('user/' . $user->uid);
    }

    $form['#user'] = drupal_anonymous_user();
    $form['#user_category'] = 'register';

    $form['#attached']['library'][] = array('system', 'jquery.cookie');
    $form['#attributes']['class'][] = 'user-info-from-cookie';

    // Start with the default user account fields.
    user_account_form($form, $form_state);

    // Attach field widgets, and hide the ones where the 'user_register_form'
    // setting is not on.
    $langcode = entity_language('user', $form['#user']);
    field_attach_form('user', $form['#user'], $form, $form_state, $langcode);
    foreach (field_info_instances('user', 'user') as $field_name => $instance) {
      if (empty($instance['settings']['user_register_form'])) {
        $form[$field_name]['#access'] = FALSE;
      }
    }

    if ($admin) {
      // Redirect back to page which initiated the create request;
      // usually admin/people/create.
      $form_state['redirect'] = $_GET['q'];
    }

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Create new account'),
    );

    $form['#validate'][] = 'user_register_validate';
    // Add the final user registration form submit handler.
    $form['#submit'][] = 'user_register_submit';

    return $form;
  }

  protected function getDurpalUserId($contact_id) {
    try {
      $domain_id = CRM_Core_Config::domainID();
      $uf = civicrm_api3('UFMatch', 'getsingle', array('contact_id' => $contact_id, 'domain_id' => $domain_id));
      return $uf['uf_id'];
    } catch (Exception $e) {
      //do nothing
    }
    return false;
  }
  
  protected function randomPassword() {
    //from http://stackoverflow.com/a/6101969
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }
  
  
}

