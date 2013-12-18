<?php

return array(
    // Set YII_DEBUG and YII_TRACE_LEVEL flags
    'yiiDebug' => true,
    'yiiTraceLevel' => 3,
    
    'configWeb' => array(
        
        'modules' => array(
            // enable the Gii tool
            'gii' => array(
                'class' => 'system.gii.GiiModule',
                'password' => 'giiPass',
                'ipFilters' => array('127.0.0.1', '::1'),
                'generatorPaths' => array(
                    'ext.awegen',
                    'bootstrap.gii',
                ),
            ),
        ),
        
        'components' => array(
            'log' => array(
                'routes' => array(
                    'file' => array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'error, warning, info',
                    ),
                ),
            ),
        ),
    ),
);
