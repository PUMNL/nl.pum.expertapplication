<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Expertapplication_Form_Report_MyApplications',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'My Applications',
      'description' => 'The applications which an SC should take care of',
      'class_name' => 'CRM_Expertapplication_Form_Report_MyApplications',
      'report_url' => 'nl.pum.expertapplication/myapplications',
      'component' => 'CiviCase',
    ),
  ),
);