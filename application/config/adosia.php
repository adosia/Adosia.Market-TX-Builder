<?php

return [

    // Plutus V2 Contracts
    'contracts' => [
        'marketplace' => [
            'script_address' => env('MARKETPLACE_CONTRACT_SCRIPT_ADDRESS'),
            'reference_tx_id' => env('MARKETPLACE_CONTRACT_REFERENCE_TX_ID')
        ],
        'invoice' => [
            'reference_tx_id' => env('INVOICE_CONTRACT_REFERENCE_TX_ID'),
        ],
        'printing_pool' => [
            'script_address' => env('PRINTING_POOL_CONTRACT_SCRIPT_ADDRESS'),
            'reference_tx_id' => env('PRINTING_POOL_CONTRACT_REFERENCE_TX_ID'),
        ],
    ],

    // Policies
    'policies' => [
        'designer' => [
            'policy_id' => env('DESIGNER_POLICY_ID'),
            'script' => env('DESIGNER_POLICY_SCRIPT'),
            'skey' => env('DESIGNER_POLICY_SKEY'),
            'vkey' => env('DESIGNER_POLICY_VKEY'),
        ],
        'purchase_order' => [
            'policy_id' => env('PURCHASE_ORDER_POLICY_ID'),
        ]
    ]

];
