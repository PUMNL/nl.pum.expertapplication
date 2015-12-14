<?php
/**
 * Expert.Reactivate API
 * 
 * Reads all contacts with contact_sub_type Expert where the expert status
 * is temporarily inactive and end date expert status is not empty
 * For every found: if end date is today or before, set status back to
 * active.
 *
 * @param array $params (empty, required for api workflow)
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws Exception when civicrm_api throws one
 */
function civicrm_api3_expert_reactivate($params) {
  $expertApplicationConfig = CRM_Expertapplication_Config::singleton();
  $expertStatusField = 'custom_'.$expertApplicationConfig->getExpertStatusCustomFieldId();
  $expertStatusEndDateField = 'custom_'.$expertApplicationConfig->getExpertStatusEndDateCustomFieldId();
  $params = array(
    'contact_sub_type' => $expertApplicationConfig->getExpertContactType(),
    'is_deleted' => 0,
    $expertStatusField => $expertApplicationConfig->getExpertTempInactOption(),
    'return' => $expertStatusEndDateField,
    'options' => array('limit' => 999999)
  );
  try {
    $contacts = civicrm_api3('Contact', 'Get', $params);
  } catch (CiviCRM_API3_Exception $ex) {
    throw new Exception('API error when retrieving contacts with API Contact Get : '.$ex->getMessage());
  }
  
  foreach ($contacts['values'] as $tempInactiveExpert) {
    if (!empty($tempInactiveExpert[$expertStatusEndDateField])) {
      $endDate = date('Ymd', strtotime($tempInactiveExpert[$expertStatusEndDateField]));
      if ($endDate <= date('Ymd')) {
        _reactivateExpert($tempInactiveExpert['contact_id']);
      }
    }
  }  
  return civicrm_api3_create_success(array(), array(), 'Expert', 'Reactivate');
}

function _reactivateExpert($entityId) {
  $expertApplicationConfig = CRM_Expertapplication_Config::singleton();
  $reActQry = 'UPDATE '.$expertApplicationConfig->getExpertDataCustomTable().
    ' SET '.$expertApplicationConfig->getExpertStatusColumn().
    ' = %1, '.$expertApplicationConfig->getExpertStatusEndDateColumn().' = %2, '
    .$expertApplicationConfig->getExpertStatusStartDateColumn().' = %3, '
    .$expertApplicationConfig->getExpertStatusReasonColumn().' = %4 WHERE entity_id = %5';
  $reActParams = array(
    1 => array($expertApplicationConfig->getExpertActiveOption(), 'String'),
    2 => array(null, 'Date'),
    3 => array(date('Ymd'), 'Date'),
    4 => array('Reactivated by automatic job', 'String'),
    5 => array($entityId, 'Positive')
  );
  CRM_Core_DAO::executeQuery($reActQry, $reActParams);
}

