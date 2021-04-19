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

  public function upgrade_1004() {
    $activity_type_id = civicrm_api3('OptionValue', 'getvalue', array('name' => 'Create Candidate Expert Account', 'return' => 'value', 'option_group_id' => 2));
    $activities = civicrm_api3('Activity', 'get', array(
      'activity_type_id' => $activity_type_id,
      'limit' => 9999999,
      'option.limit' => 9999999,
    ));
    foreach($activities['values'] as $activity) {
      try {
        CRM_Case_BAO_Case::deleteCaseActivity($activity['id']);
        CRM_Activity_BAO_Activity::deleteActivityContact($activity['id']);
        $params = array('id' => $activity['id']);
        CRM_Activity_BAO_Activity::deleteActivity($params);
      } catch (Exception $e) {
        throw $e;
      }
    }
    $activity_type_id = civicrm_api3('OptionValue', 'getvalue', array('name' => 'Create Candidate Expert Account', 'return' => 'id', 'option_group_id' => 2));
    civicrm_api3('OptionValue', 'delete', array('id' => $activity_type_id));

    // Delete the setting.
    $setting = new CRM_Core_DAO_Setting();
    $setting->group_name = 'nl.pum.expertapplication';
    $setting->name = 'new_expert_role_activities';
    if ($setting->find()) {
      $setting->delete();
    }

    return true;
  }

  /**
   * Upgrade 1005 - Add expert status 'Onboarding'
   */
  public function upgrade_1005() {
    try {
      $params_og_expertstatus = array(
        'version' => 3,
        'sequential' => 1,
        'title' => 'Expert status',
      );
      $result_og_expertstatus = civicrm_api('OptionGroup', 'getsingle', $params_og_expertstatus);

      if(!empty($result_og_expertstatus['id'])){
        $params = array(
          'version' => 3,
          'sequential' => 1,
          'option_group_id' => $result_og_expertstatus['id'],
          'name' => 'Onboarding',
          'label' => 'Onboarding',
          'value' => 'Onboarding',
          'weight' => 40,
          'description' => '',
          'is_reserved' => 1,
        );
        $result = civicrm_api('OptionValue', 'create', $params);
      }
    } catch(Exception $e) {
      return FALSE;
    }

    if($result['count'] != 1 || $result['is_error'] == 1){
      return FALSE;
    }

    return TRUE;
  }
}
