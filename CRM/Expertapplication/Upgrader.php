<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Expertapplication_Upgrader extends CRM_Expertapplication_Upgrader_Base {

  public function upgrade_1001() {
    $this->executeCustomDataFile('xml/expert_data_agree_privacy_scheme.xml');
    return TRUE;
  }

  public function upgrade_1003() {
    $activity_type_group_id = civicrm_api3('OptionGroup', 'getvalue', array(
      'name' => 'activity_type',
      'return' => 'id',
    ));
    CRM_Core_DAO::executeQuery("UPDATE civicrm_option_value SET label = 'Filled Out PUM CV' WHERE name = 'Fill Out PUM CV' AND option_group_id = %1", array(
      1 => array($activity_type_group_id, 'Integer')
    ));
    return TRUE;
  }
  
}
