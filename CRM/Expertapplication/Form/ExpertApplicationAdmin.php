<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Expertapplication_Form_ExpertApplicationAdmin extends CRM_Core_Form {
  function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Settings for expert application'));
    
    // add form elements
    $this->add(
      'select', // field type
      'drupal_role', // field name
      'Drupal role for new expert', // field label
      $this->getDrupalRolesOptions(), // list of options
      true // is required
    );
    
    $this->add(
      'select', // field type
      'activity_for_user_role', // field name
      'Grant drupal role on completion of', // field label
      $this->getActivityTypes(), // list of options
      true, // is required
      array('multiple' => true)
    );
    
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }
  
  function setDefaultValues() {
    parent::setDefaultValues();
    
    $config = CRM_Expertapplication_Config::singleton();
    $values['drupal_role'] = $config->getDrupalRole();
    $values['activity_for_user_role'] = $config->getActivityTypes();

    return $values;
  }

  function postProcess() {
    $values = $this->exportValues();    
    
    CRM_Core_BAO_Setting::setItem($values['drupal_role'], 'nl.pum.expertapplication', 'new_expert_role_id');
    CRM_Core_BAO_Setting::setItem(serialize($values['activity_for_user_role']), 'nl.pum.expertapplication', 'new_expert_role_activities');
    
    CRM_Core_Session::setStatus(ts('Saved expert application settings'), ts('Expert application settings'), 'success');
        
    parent::postProcess();
  }

  function getDrupalRolesOptions() {    
    $options = array(
      '' => ts('- select -')
    );
    
    $config = CRM_Expertapplication_Config::singleton();
    $roles = $config->getDrupalRoles();
    foreach($roles as $rid => $role) {
      $options[$rid] = $role;
    }
    return $options;
  }
  
  function getActivityTypes() {
    $gid = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => 'activity_type'));
    $activityTypes = CRM_Core_OptionGroup::values('activity_type');
    foreach($activityTypes as $value => $option) {
      $options[$value] = $option;
    }
    return $options;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
