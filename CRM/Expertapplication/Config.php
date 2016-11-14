<?php

class CRM_Expertapplication_Config {
  
  private static $_singleton;
  
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
  
  private $expert_application_case_type;
  
  private function __construct() {
    /*
     * config options required for api Expert Reactivate
     */
    $this->setExpertCustomData();
    $this->setExpertContactType();
    
    $case_type_id = civicrm_api3('OptionGroup', 'getvalue', array('name' => 'case_type', 'return' => 'id'));
    $this->expert_application_case_type = civicrm_api3('OptionValue', 'getsingle', array('name' => 'Expertapplication', 'option_group_id' => $case_type_id));
  }
  
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Expertapplication_Config();
    }
    return self::$_singleton;
  }
  
  public function getExpertApplicationCaseType($key='id') {
    return $this->expert_application_case_type[$key];
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
      if (!isset($optionValue['name'])) {
        continue;
      }
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