<?php


return [

    'pause_job_delay' => [
        1 => 1,
        2 => 5,
        3 => 30,
        4 => 60,
        5 => 3600,
        6 => 84600,
    ],

    'pause_request_delay' => [
        1 => 1,
        2 => 5,
        3 => 10,
        4 => 15,
        5 => 30,
    ],

    'queues' => [
        'default', 'email', 'webinar', 'webhook', 'parents'
    ],

    'request_status' => [
        'from' => 500,
        'to' => 600
    ]

];