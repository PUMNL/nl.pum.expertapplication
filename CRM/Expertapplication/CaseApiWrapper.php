<?php

/**
 * Class for ExpertApplication Case API wrapper
 * (issue 3095 update case ExpertApplication because there should only be 1
 *  for each expert https://redmine.pum.nl/issues/3095)
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 27 Jun 2016
 * @license AGPL-3.0
 */
class CRM_Expertapplication_CaseApiWrapper implements API_Wrapper {

  function fromApiInput($apiRequest) {
    // only if case type is expert application
    if ($this->isExpertApplication($apiRequest['params'])) {
      // only if coming from webform
      if ($this->isRequestFromWebform()) {
        // check if there is an existing case and add case_id to params if there is
        $existingCaseId = $this->findExpertApplication($apiRequest['params']);
        CRM_Core_Error::debug('existingCaseId', $existingCaseId);
        if ($existingCaseId) {
          $apiRequest['params']['id'] = $existingCaseId;
        }
      }
    }
    return $apiRequest;
  }

  /**
   * @param $apiParams
   * @return bool
   * @throws Exception when error from API OptionValue
   */
  private function isExpertApplication($apiParams) {
    $config = CRM_Expertapplication_Config::singleton();
    $expertAppCaseTypeId = $config->getExpertApplicationCaseType('value');
    try {
      if (isset($apiParams['case_type_id']) && $apiParams['case_type_id'] == $expertAppCaseTypeId) {
        return TRUE;
      } else {
        return FALSE;
      }
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a case type ExpertApplication in '.__METHOD__
        .', contact your system administrator. Error from API OptionValue Getvalue: '.$ex->getMessage());
    }
  }

  /**
   * Method to check if request comes in from webform Expert Application
   *
   * @return bool
   */
  private function isRequestFromWebform() {
    $requestData = CRM_Utils_Request::exportValues();
    // retrieve node id from request and check if node is Expert Application form
    if (isset($requestData['q'])) {
      $qParts = explode('node/', $requestData['q']);
      if (isset($qParts[1])) {
        $nodeTitle = db_query("SELECT title FROM {node} where (nid = {$qParts[1]})")->fetchField();
        if ($nodeTitle == 'Expert Application') {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Method to retrieve expert application case_if we already have one
   *
   * @param $apiParams
   * @return bool|string
   */
  private function findExpertApplication($apiParams) {
    if (isset($apiParams['contact_id'])) {
      $config = CRM_Expertapplication_Config::singleton();
      $query = 'SELECT MAX(cc.id) FROM civicrm_case cc JOIN civicrm_case_contact co ON cc.id = co.case_id
        WHERE co.contact_id = %1 AND cc.case_type_id LIKE %2 AND cc.is_deleted = %3';
      $params = array(
        1 => array($apiParams['contact_id'], 'Integer'),
        2 => array('%'.$config->getExpertApplicationCaseType('value').'%', 'String'),
        3 => array(0, 'Integer')
      );
      $caseId = CRM_Core_DAO::singleValueQuery($query, $params);
      if (!empty($caseId)) {
        return $caseId;
      }
    }
    return FALSE;
  }

  /**
   * Void function required as part of abstract class API_Wrapper
   * @param $apiRequest
   * @param $result
   * @return mixed
   */
  function toApiOutput($apiRequest, $result) {
    return $result;
  }
}