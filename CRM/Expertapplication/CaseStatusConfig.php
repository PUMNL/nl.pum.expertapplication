<?php

class CRM_Expertapplication_CaseStatusConfig {
  
  protected static $singleton;
  
  protected $reject;
  
  protected function __construct() {
    $case_status_id = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => 'case_status'));
    $this->reject = civicrm_api3('OptionValue', 'getsingle', array('name' => 'Rejected', 'option_group_id' => $case_status_id));
  }
  
  /**
   * 
   * @return CRM_Expertapplication_CaseStatusConfig
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Expertapplication_CaseStatusConfig();
    }
    return self::$singleton;
  }
  
  public function getCaseStatusReject($key) {
    return $this->reject[$key];
  }
  
}

