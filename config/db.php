<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../data/tabwiter.db',
    'charset' => 'utf8',

    // Enable foreign keys for SQLite
    'on afterOpen' => function ($event) {
        /** @var yii\db\Connection $db */
        $event->sender->createCommand('PRAGMA foreign_keys = ON')->execute();
    },

    // Schema cache for production
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
