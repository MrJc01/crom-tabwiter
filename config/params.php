<?php

return [
    // Post constraints
    'postMaxLength' => 500,

    // Ephemeral engine
    'decayPerDay' => 1,
    'deathThreshold' => -10,

    // Guest cleanup
    'guestInactiveHours' => 24,

    // TabNews API
    'tabnewsApiBase' => 'https://www.tabnews.com.br/api/v1/',
    'tabnewsCacheTtl' => 300, // 5 minutes cache
];
