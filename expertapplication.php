<?php

require_once 'expertapplication.civix.php';
/**
 * Implements hook_civicrm_apiWrappers()
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_apiWrappers
 */
function expertapplication_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  $apiEntity = strtolower($apiRequest['entity']);
  $apiAction = strtolower($apiRequest['action']);
  if ($apiEntity == 'case') {
    if ($apiAction == 'create') {
      // process apiRequest
      $wrappers[] = new CRM_Expertapplication_CaseApiWrapper();
    }
  }
}

function expertapplication_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Case_Form_CaseView' || $formName == 'CRM_Case_Form_EditClient') {
   //deny access to the expert application case when the logged in user is the client of the case
    CRM_Expertapplication_DenyAccessExpertApplication::buildForm($formName, $form);
  }

  if ($formName == 'CRM_Case_Form_CaseView'){
    $caseType = $form->getVar('_caseType');
    if ($caseType == 'Expertapplication') {
      CRM_Core_Resources::singleton()->addScriptFile('nl.pum.expertapplication', 'js/cv_approval_process.js');
    }
  }
}

function expertapplication_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
  /* When changing expert status to 'Active', start date should be filled */
  if($formName == 'CRM_Contact_Form_CustomData'){
    $cf_expert_status = civicrm_api('CustomField', 'getsingle', array('version' => 3, 'sequential' => 1, 'name' => 'expert_status', 'custom_group_name' => 'expert_data'));

    if(!empty($form->_groupID) && $form->_groupID == $cf_expert_status['custom_group_id']){
      $cg_expertdata = civicrm_api('CustomGroup', 'getsingle', array('version' => 3, 'sequential' => 1, 'name' => 'expert_data'));

      if(is_int((int)$form->_entityId)){
        $q_expertdata_id = CRM_Core_DAO::singleValueQuery("SELECT id FROM ".$cg_expertdata['table_name']." WHERE entity_id = %1", array(1=> array((int)$form->_entityId, 'Integer')));
      }

      if(is_int((int)$q_expertdata_id)){
        if(!empty($form->_submitValues['custom_'.$cf_expert_status['id'].'_'.$q_expertdata_id]) && $form->_submitValues['custom_'.$cf_expert_status['id'].'_'.$q_expertdata_id] == 'Active'){
          $cf_expert_startdate = civicrm_api('CustomField', 'getsingle', array('version' => 3, 'sequential' => 1, 'name' => 'expert_status_start_date', 'custom_group_name' => 'expert_data'));

          if(empty($form->_submitValues['custom_'.$cf_expert_startdate['id'].'_'.$q_expertdata_id])){
            $errors['custom_'.$cf_expert_startdate['id'].'_'.$q_expertdata_id] = ts("Start date expert status cannot be empty when status is 'Active'");
          }
        }
      }
    }
  }
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function expertapplication_civicrm_config(&$config) {
  _expertapplication_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function expertapplication_civicrm_xmlMenu(&$files) {
  _expertapplication_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function expertapplication_civicrm_install() {
  return _expertapplication_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function expertapplication_civicrm_uninstall() {
  return _expertapplication_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function expertapplication_civicrm_enable() {
  return _expertapplication_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function expertapplication_civicrm_disable() {
  return _expertapplication_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function expertapplication_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _expertapplication_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function expertapplication_civicrm_managed(&$entities) {
  return _expertapplication_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function expertapplication_civicrm_caseTypes(&$caseTypes) {
  _expertapplication_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function expertapplication_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _expertapplication_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
