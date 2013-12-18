<?php

return array(
    // Set yiiPath (relative to Environment.php)
    'yiiPath' => dirname(__FILE__) . '/../../lib/yii/framework/yii.php',
    'yiicPath' => dirname(__FILE__) . '/../../lib/yii/framework/yiic.php',
    'yiitPath' => dirname(__FILE__) . '/../../lib/yii/framework/yiit.php',
    // Set YII_DEBUG and YII_TRACE_LEVEL flags
    'yiiDebug' => true,
    'yiiTraceLevel' => 0,
    // Static function Yii::setPathOfAlias()
    'yiiSetPathOfAlias' => array(
        'lib' => dirname(__FILE__) . '/../../lib/',
    ),
    // This is the main Web application configuration. Any writable
    // CWebApplication properties can be configured here.
    'configWeb' => array(
        'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
        'name' => 'WM Pot',
        'language' => 'de',
        'timezone' => 'Europe/Zurich',
        // Preload components
        'preload' => array(
            'log',
            'bootstrap',
            'less',
        ),
        // autoloading model and component classes
        'import' => array(
            // application
            'application.models.*',
            'application.models.entities.*',
            'application.views.*',
            'application.components.*',
            //Widgets
            'application.widgets.*',
            // bootstrap
            'application.extensions.bootstrap.*',
        ),
        // application components
        'components' => array(
            'user' => array(
                'class' => 'WebUser',
                // enable cookie-based authentication
                'allowAutoLogin' => true,
                'loginUrl' => array('/site/login'),
            ),
            'authManager' => array(
                'class' => 'AuthManager',
            ),
            // enable URLs in path-format
            'urlManager' => array(
                'caseSensitive' => true,
                'urlFormat' => 'path',
                'showScriptName' => true,
                'rules' => array(
                    '<controller:\w+>/<id:\d+>' => '<controller>/view',
                    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                ),
            ),
            // Database
            'db' => array(
                'charset' => 'utf8',
                'initSQLs' => array(
                    'SET DATEFORMAT ymd',
                ),
                'enableParamLogging' => true,
            ),
            // Error-Handler
            'errorHandler' => array(
                'errorAction' => '/site/error',
            ),
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    'file' => array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'error, warning',
                    ),
                ),
            ),
            'session' => array(
                'timeout' => 24 * 3600, // setzt session.gc_maxlifetime im php.ini
            ),
            'format' => array(
                'class' => 'Formatter',
            ),
            'bootstrap' => array(
                'class' => 'ext.YiiBooster.src.components.Bootstrap',
                'fontAwesomeCss' => true
            ),
            'less' => array(
                'class' => 'application.components.LessCompiler',
                'forceCompile' => false,
                'source' => 'application.less',
                'destination' => 'webroot.css',
                'files' => array(
                    'screen.less',
                    'print.less'
                ),
            ),
        ),
        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params' => array(
            // Website
            'brand' => 'WM Pot',
            'slogan' => 'WM Pot',
        ),
    ),
    'configConsole' => array(
        'import' => 'inherit',
        'basePath' => 'inherit',
        'components' => 'inherit',
    ),
);
