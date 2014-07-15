<?php

class CRM_Expertapplication_Config {
  
  private static $_singleton;
  
  private $activity_types;
  
  private $drupal_role = false;
  
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
  
}

