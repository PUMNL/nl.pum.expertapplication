<?php

class CRM_Expertapplication_DenyAccessExpertApplication {
  
  public static function buildForm($formName, &$form) {    
    $hasAccess = true;
    
    $caseId = false;
    if ($formName == 'CRM_Case_Form_CaseView') {
      $caseId = $form->getVar('_caseID');
    } elseif($formName == 'CRM_Case_Form_EditClient') {
      $caseId = $form->getVar('_caseId');
    }
    
    if (empty($caseId)) {
      return;
    }
    
    $session = CRM_Core_Session::singleton();
    if (!$session->get('userID')) {
      return;
    }
    
    $config = CRM_Expertapplication_Config::singleton();
    try {      
      $caseParams['id'] = $caseId;
      $case = civicrm_api3('Case', 'getsingle', $caseParams);
      if ($case['case_type_id'] == $config->getExpertApplicationCaseType('value')) {
       //case is an ExpertApplication and user is the expert 
       foreach($case['client_id'] as $client_id) {
         if ($client_id == $session->get('userID')) {
           $hasAccess = false;
           break;
         }
       } 
      }
    } catch (Exception $ex) {
      return; //case could not be found
    }
    
    if (!$hasAccess) {
      $session->setStatus('You do not have access to view this case', 'Access denied', 'Alert');
      $session->popUserContext(); //one popUserContext does return to the case view screen
      $userContext = $session->popUserContext();
      CRM_Utils_System::redirect($userContext);
    }
    
    
  }
  
}

