<?php

$db = require __DIR__ . '/db.php';
// use separate sqlite file for tests to avoid clobbering development data
$db['dsn'] = 'sqlite:' . __DIR__ . '/../data/tabwiter_test.db';

return $db;
