<?php

class CRM_Expertapplication_RejectionConfig {
  
  protected static $_singleton;
  
  public $custom_group_rejection;
  
  public $reject_field;
  
  protected function __construct() {
    $this->custom_group_rejection = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'Assessment_Expert_Application'));
    $this->reject_field = civicrm_api3('CustomField', 'getsingle', array('name' => 'Reject_Expert_Application', 'custom_group_id' => $this->custom_group_rejection['id']));
    
  }
  
  /**
   * 
   * @return CRM_Expertapplication_RejectionConfig
   */
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Expertapplication_RejectionConfig();
    }
    return self::$_singleton;
  }
  
  public function getCustomGroupRejection($key) {
    return $this->custom_group_rejection[$key];
  }
  
  public function getCustomFieldRejection($key) {
    return $this->reject_field[$key];
  }
  
}

