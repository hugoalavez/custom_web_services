<?php

defined('MOODLE_INTERNAL') || die();
$functions = array(
    'local_custom_web_services_get_users_by_timemodified' => array(
        'classname' => 'local_custom_web_services_external',
        'classpath' => 'local/custom_web_services/externallib.php',
        'description' => 'Get Users modified by the last seconds',
        'type' => 'read',
        'ajax' => true,
    )
);

$services = array(
    'TICSS Custom Web Services ' => array(
        'functions' => array(
            'local_custom_web_services_get_users_by_timemodified'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
