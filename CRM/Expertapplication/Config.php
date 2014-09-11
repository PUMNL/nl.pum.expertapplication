<?php

class CRM_Expertapplication_Config {
  
  private static $_singleton;
  
  private $activity_types;
  
  private $drupal_role = false;
  
  private $expertStatusColumn = null;
  private $expertStatusCustomFieldId = null;
  private $expertStatusEndDateColumn = null;
  private $expertStatusStartDateColumn = null;
  private $expertStatusReasonColumn = null;
  private $expertStatusEndDateCustomFieldId = null;
  private $expertDataCustomTable = null;
  private $expertTempInactOption = null;
  private $expertActiveOption = null;
  private $expertContactType = null;
  
  private function __construct() {
    $this->activity_types = unserialize(CRM_Core_BAO_Setting::getItem('nl.pum.expertapplication', 'new_expert_role_activities'));
    if (!is_array($this->activity_types)) {
      $this->activity_types = array();
    }
    
    $rid = CRM_Core_BAO_Setting::getItem('nl.pum.expertapplication', 'new_expert_role_id');
    $roles = $this->getDrupalRoles();
    if (isset($roles[$rid])) {
      $this->drupal_role = $rid;
    }
    /*
     * config options required for api Expert Reactivate
     */
    $this->setExpertCustomData();
    $this->setExpertContactType();
  }
  
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Expertapplication_Config();
    }
    return self::$_singleton;
  }
  
  public function getActivityTypes() {
    return $this->activity_types;
  }
  
  public function getDrupalRole() {
    return $this->drupal_role;
  }
  
  public function getDrupalRoles() {
    $roles = array();
    if (function_exists('user_roles')) {
      $roles = user_roles(true); 
    }
    return $roles;
  }
  
  public function getExpertContactType() {
    return $this->expertContactType;
  }
  
  public function getExpertDataCustomTable() {
    return $this->expertDataCustomTable;
  }
  
  public function getExpertStatusColumn() {
    return $this->expertStatusColumn;
  }
  
  public function getExpertStatusEndDateColumn() {
    return $this->expertStatusEndDateColumn;
  }
  
  public function getExpertStatusStartDateColumn() {
    return $this->expertStatusStartDateColumn;
  }
  
  public function getExpertStatusReasonColumn() {
    return $this->expertStatusReasonColumn;
  }
  
  public function getExpertStatusCustomFieldId() {
    return $this->expertStatusCustomFieldId;
  }
  
  public function getExpertStatusEndDateCustomFieldId() {
    return $this->expertStatusEndDateCustomFieldId;
  }
  
  public function getExpertTempInactOption() {
    return $this->expertTempInactOption;
  }
  
  public function getExpertActiveOption() {
    return $this->expertActiveOption;
  }
  
  private function setExpertContactType() {
    $this->expertContactType = 'Expert';
  }
  
  private function setExpertCustomData() {
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => 'expert_data'));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception("Could not find a custom group with name 'expert_data', "
        . "error from API CustomGroup Getsingle : ".$ex->getMessage());
    }
    $this->expertDataCustomTable = $customGroup['table_name'];
    $this->setExpertStatusCustomFields($customGroup['id']);
  }
  
  private function setExpertStatusCustomFields($customGroupId) {
    try {
      $customFields = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $customGroupId));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception("Could not find custom fields in custom group 'expert_data', "
        . "error from API CustomField Get : ".$ex->getMessage());
    }
    foreach ($customFields['values'] as $customField) {
      switch ($customField['name']) {
        case 'expert_status':
          $this->expertStatusColumn = $customField['column_name'];
          $this->expertStatusCustomFieldId = $customField['id'];
          $this->setExpertStatusOptions($customField['option_group_id']);
          break;
        case 'expert_status_end_date':
          $this->expertStatusEndDateCustomFieldId = $customField['id'];
          $this->expertStatusEndDateColumn = $customField['column_name'];
          break;
        case 'expert_status_start_date':
          $this->expertStatusStartDateColumn = $customField['column_name'];
          break;
        case 'expert_status_reason':
          $this->expertStatusReasonColumn = $customField['column_name'];
          break;
      }
    }
  }
  
  private function setExpertStatusOptions($optionGroupId) {
    try {
      $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $optionGroupId));
    } catch (CiviCRM_API3_Exception $ex) {
      $this->expertTempInactOption = null;
      throw new Exception("Could not find option values in option group ".$optionGroupId.
        ", error from API OptionValue Get : ".$ex->getMessage());
    }
    foreach($optionValues['values'] as $optionValue) {
      switch ($optionValue['name']) {
        case 'Active':
          $this->expertActiveOption = $optionValue['value'];
          break;
        case 'Temporarily_inactive':
          $this->expertTempInactOption = $optionValue['value'];
          break;
      }
    }
  }
}