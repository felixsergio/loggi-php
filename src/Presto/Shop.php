<?php

namespace FelixLoggi\Presto;

use FelixLoggi\Request;
use FelixLoggi\GraphQL;

class Shop
{

    public function all()
    {
        $query = GraphQL::createObject(
            'query',
            'allShops',
            [
                'edges' => [
                    'node' => [
                        'name',
                        'pickupInstructions',
                        'pk',
                        'address' => [
                            'pos',
                            'addressSt',
                            'addressData'
                        ],
                        'chargeOptions' => [
                            'label',
                        ],
                        'externalId',
                    ],
                ]
            ]
        );

        return new Request('post', '/', $query);
    }

    public function create(array $data)
    {

        $input = [
            'name' => null,
            'addressCep' => null,
            'addressNumber' => null,
            'addressComplement' => null,
            'phone' => null,
            'companyId' => null,
            'pickupInstructions' => null,
            'numberOfRadialZones' => null,
            'externalId' => null,
            'productVersion: start'
        ];

        $input = array_merge($input, $data);

        $query = GraphQL::createObject(
            'mutation',
            'createShop',
            [
                'shop' => [
                    'pk',
                    'name',
                    'address' => [
                        'label',
                    ],
                    'pickupInstructions',
                ]
            ],
            [
                'input' => $input
            ]
        );

        return new Request('post', '/', $query);
    }
}
