<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:Expert.Reactivate',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Call Expert.Reactivate API',
      'description' => 'Call Expert.Reactivate API',
      'run_frequency' => 'Daily',
      'api_entity' => 'Expert',
      'api_action' => 'Reactivate',
      'parameters' => '',
    ),
  ),
);