<?php

return [
    'class' => 'yii\db\Connection',
    // local sqlite file for lightweight storage
    'dsn' => 'sqlite:' . __DIR__ . '/../data/tabwiter.db',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
