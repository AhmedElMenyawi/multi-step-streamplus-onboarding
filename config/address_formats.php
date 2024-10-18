<?php

return [
    'AE' => [
        'state_label' => 'Emirate',
        'postal_code_label' => 'Postal Code',
        'state_required' => true,
        'postal_code_pattern' => null
    ],
    'DE' => [
        'state_label' => 'State',
        'postal_code_label' => 'Postal Code',
        'state_required' => true,
        'postal_code_pattern' => '/^\d{5}$/'
    ],
    'EG' => [
        'state_label' => 'Governorate',
        'postal_code_label' => 'Postal Code',
        'state_required' => true,
        'postal_code_pattern' => '/^\d{5}$/'
    ],
    'default' => [
        'state_label' => 'State/Province',
        'postal_code_label' => 'Postal Code',
        'state_required' => true,
        'postal_code_pattern' => null
    ],
];
