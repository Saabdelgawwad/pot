<?php

// set environment
require_once(dirname(__FILE__) . '/../protected/extensions/yii-environment/Environment.php');
$env = new Environment();

// set debug and trace level
defined('YII_DEBUG') or define('YII_DEBUG', $env->yiiDebug);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', $env->yiiTraceLevel);

// Fatal Errors abfangen und loggen
if (!YII_DEBUG) {
    // Fehler-Anzeige ausschalten
    ini_set('display_errors', 0);

    register_shutdown_function(function() {
        $error = error_get_last();

        // Nur Parser- und Fatal Errors abfangen
        if ($error['type'] === E_PARSE || $error['type'] === E_ERROR) {
            Yii::log($error['message'] . ' in file ' . $error['file'] . ' on line ' . $error['line'], 'error', 'fatal');
            header('location:/fatalError.php');
        }
    });
}

// run Yii app
require_once($env->yiiPath);
$env->runYiiStatics(); // like Yii::setPathOfAlias()
Yii::createWebApplication($env->configWeb)->run();
