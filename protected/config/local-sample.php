<?php

return array(
    'configWeb' => array(

        'components' => array(
            // Database
            'db' => array(
                'connectionString' => 'sqlsrv:server=localhost\DBSERVER;Database=DBNAME',
                'username' => 'DBUSER',
                'password' => 'DBPASS',
            ),
            // Logging
            'log' => array(
                'routes' => array(
                    array(
                        'class' => 'CWebLogRoute',
                        'showInFireBug' => true,
                    ),//*/
                    array(
                        'class' => 'CProfileLogRoute',
                        'report' => 'summary'
                    ),//*/
                ),
            ),
        ),
    ),
);
