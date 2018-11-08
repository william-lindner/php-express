<?php

return [
    /**
     * The list of masked properties in the .ini you would like for no one
     * to be able to access.
     */
    'protected' => [
        'session', 'database', 'mail',
    ],
];
