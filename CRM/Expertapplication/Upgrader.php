<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Expertapplication_Upgrader extends CRM_Expertapplication_Upgrader_Base {

  public function upgrade_1001() {
    $this->executeCustomDataFile('xml/expert_data_agree_privacy_scheme.xml');
    return TRUE;
  }

  public function upgrade_1002() {
    $this->executeCustomDataFile('xml/expert_application_approve.xml');
    return TRUE;
  }
  
}
