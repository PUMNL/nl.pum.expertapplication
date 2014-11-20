<?php

class CRM_Expertapplication_Hooks_CustomCaseStatusReject {
  
  public static function custom($op, $groupID, $entityID, &$params) {
    if ($op != 'edit' && $op != 'create') { //create doesn't work, we use the pre hook for create
      return;
    }

    $config = CRM_Expertapplication_RejectionConfig::singleton();
    if ($config->getCustomGroupRejection('id') != $groupID) {
      return;
    }
    
    //ok, requirements met
    $values = array();
    foreach($params as $param) {
      $values[$param['custom_field_id']] = $param['value'];
    }
    
    if (empty($values[$config->getCustomFieldRejection('id')])) {
      return;
    }
    
    $status_config = CRM_Expertapplication_CaseStatusConfig::singleton();
    
    //update case status to reject
    $params = array();
    $params['id'] = $entityID;
    $params['status_id'] = $status_config->getCaseStatusReject('value');
    civicrm_api3('Case', 'create', $params);
  }
  
}
