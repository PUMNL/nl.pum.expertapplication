<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Expertapplication_CiviRulesCondition_ExpertApplicationAccepted extends CRM_Civirules_Condition {

  private static $acceptedCustomField;

  protected static function getAcceptedCustomField() {
    if (!self::$acceptedCustomField) {
      $custom_group_id = civicrm_api3('CustomGroup', 'getvalue', array('return' => 'id', 'name' => 'approve_application'));
      self::$acceptedCustomField = civicrm_api3('CustomField', 'getsingle', array('custom_group_id' => $custom_group_id, 'name' => 'accepted'));
    }
    return self::$acceptedCustomField;
  }

  public function getExtraDataInputUrl($ruleConditionId) {
    return false;
  }

  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    if ($triggerData instanceof CRM_Civirules_TriggerData_Edit) {
      $case = $triggerData->getEntityData('Case');
      $originalData = $triggerData->getOriginalData();
      $accepted_custom_field = self::getAcceptedCustomField();
      $accepted_custom_field_key = 'custom_'.$accepted_custom_field['id'];
      if (isset($originalData[$accepted_custom_field_key]) && isset($case[$accepted_custom_field_key]) && $originalData[$accepted_custom_field_key] != $case[$accepted_custom_field_key] && $case[$accepted_custom_field_key] == '1') {
        return true;
      }
    }
    return false;
  }


}