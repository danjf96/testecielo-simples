<?php

return [
    'MerchantId' => env('CIELO_ID'),
    'MerchantKey' => env('CIELO_KEY'),
    'environment'  => env('CIELO_ENV', 'sandbox')
];

?>