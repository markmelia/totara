<?php

$restrictionoptions = array(
    array(
        'funcname' => 'own_records',  // function called to apply restriction
        'title' => 'Self',  // for text describing option in admin settings
        'field' => 'base.userid',      // field to apply limit to
        'capability' => 'moodle/local:viewownreports', // cap required, if not set then restriction can be applied without needing any capability
        'default' => '1', // if 1, this setting is checked for new reports
    ),
    array(
        'funcname' => 'staff_records',
        'title' => 'Immediate Subordinates',
        'field' => 'base.userid',
        'capability' => 'moodle/local:viewstaffreports',
        'default' => '0',
    ),
    array(
        'funcname' => 'local_records',
        'title' => 'Current Local staff',
        'field' => 'base.userid',
        'capability' => 'moodle/local:viewlocalreports',
        'default' => '0',
    ),
    array(
        'funcname' => 'local_completed_records',
        'title' => 'Those Completed Locally',
        'field' => 'base.organisationid',
        'capability' => 'moodle/local:viewlocalreports',
        'default' => '0',
    ),
);
