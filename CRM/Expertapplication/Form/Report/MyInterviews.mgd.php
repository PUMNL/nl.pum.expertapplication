<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Expertapplication_Form_Report_MyInterviews',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'My Interviews',
      'description' => 'My Interviews in which an RCT takes part',
      'class_name' => 'CRM_Expertapplication_Form_Report_MyInterviews',
      'report_url' => 'nl.pum.expertapplication/myinterviews',
      'component' => 'CiviCase',
    ),
  ),
);