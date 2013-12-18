<?php

return array(
    // Set YII_DEBUG and YII_TRACE_LEVEL flags
    'yiiDebug' => true,
    'yiiTraceLevel' => 3,
    
    'configWeb' => array(

        'components' => array(
            // Fixture Manager fürs Testen
            'fixture' => array(
                'class' => 'system.test.CDbFixtureManager',
            ),
        ),
    ),
);
