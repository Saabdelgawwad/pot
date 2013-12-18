<?php

return array(
    // Set YII_DEBUG and YII_TRACE_LEVEL flags
    'yiiDebug' => false,
    'yiiTraceLevel' => 0,

    'configWeb' => array(

        'components' => array(
            // Database
            'db' => array(
                'connectionString' => 'sqlsrv:server=localhost\DBSERVER;Database=DBNAME',
                'username' => 'DBUSER',
                'password' => 'DBPASSWORD',
            ),
            
            // Logging
            'log' => array(
                'routes' => array(
                    // Email bei Fehlern
                    'email' => array(
                        'class' => 'CEmailLogRoute',
                        'levels' => 'error',
                        'filter' => array(
                            'class' => 'LogCategoryFilter',
                            'ignoreCategories' => array(
                                'exception.CHttpException.403',
                                'exception.CHttpException.404',
                            ),
                        ),
                        'emails' => 'traveladvice@kssg.ch',
                    ),
                ),
            ),
        ),
        
        'params' => array(
            // SMTP Authentication
            'smtpUsername' => 'traveladvice@kssg.ch',
            'smtpPassword' => 'geheim',
            'smtpPort' => 465,
            'smtpHost' => 'ssl://smtp.kssg.ch',
        ),
    ),
);
