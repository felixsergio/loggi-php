<?php

namespace FelixLoggi\Presto;

use FelixLoggi\Request;
use FelixLoggi\GraphQL;

class Order
{
    public function create(array $data)
    {

        $input = [
            'shopId' => null,
            'trackingKey' => 'tracking_key',
            'pickups' => [
                [
                    'address' => [
                        'lat' => null,
                        'lng' => null,
                        'address' => null,
                        'complement' => null,
                    ]
                ]
            ],
            'packages' => [
                [
                    'pickupIndex' => null,
                    'recipient' => [
                        'name' => null,
                        'phone' => null,
                    ],
                    'address' => [
                        'lat' => null,
                        'lng' => null,
                        'address' => null,
                        'complement' => null,
                    ],
                    'charge' => [
                        'value' => null,
                        'method' => null,
                        'change' => null,
                    ],
                    'dimensions' => [
                        'width' => null,
                        'height' => null,
                        'length' => null,
                    ]
                ]
            ]
        ];

        $input = array_merge($input, $data);

        $query = GraphQL::createObject(
            'mutation',
            'createOrder',
            [
                'success',
                'shop' => [
                    'pk',
                    'name'
                ],
                'orders' => [
                    'pk',
                    'trackingKey',
                    'packages' => [
                        'pk',
                        'status',
                        'pickupWaypoint' => [
                            'index',
                            'indexDisplay',
                            'eta',
                            'legDistance'
                        ],
                        'waypoint' => [
                            'index',
                            'indexDisplay',
                            'eta',
                            'legDistance'
                        ]
                    ]
                ],
                'errors' => [
                    'field',
                    'message'
                ]
            ],
            [
                'input' => $input
            ]
        );

        return new Request('post', '/', $query);
    }

    public function update(array $data)
    {

        $input = [
            'shouldExecute' => false,
            'shouldAddReturn' => true,
            'orderId' => null,
            'packages' => [
                [
                    'pk' => null,
                    'payload' => [
                        'complement' => null,
                    ],
                ],
                [
                    'pk' => null,
                    'payload' => [
                        'cancellation' => true,
                    ],
                ]
            ],
        ];

        $input = array_merge($input, $data);

        $query = GraphQL::createObject(
            'mutation',
            'retailEditOrder',
            [
                'errors' => [
                    'field',
                    'message',
                    'title',
                ],
                'packagesDraft' => [
                    'pk',
                    'draftStatus',
                ],
                'diff' => [
                    'pricing' => [
                        'oldValue',
                        'newValue'
                    ],
                    'packages' => [
                        'pk',
                        'editType',
                        'label',
                        'oldValue',
                        'newValue',
                    ]
                ]
            ],
            [
                'input' => $input
            ]
        );

        return new Request('post', '/', $query);
    }

    public function remake(int $orderId)
    {
        $query = sprintf('
            mutation {
              redoOrder(input: {id: %d}) {
                success
                order {
                  id
                  pk
                  status
                  packages {
                    pk
                    status
                    statusCode
                    statusCodeDisplay
                  }
                }
              }
            }', $orderId);

        return new Request('post', '/', $query);
    }

    public function estimateOnRoute(array $data)
    {

        $input = [
            'shopId' => null,
            'pickups' => [
                'address' => [
                    'lat' => null,
                    'lng' => null,
                    'address' => null,
                    'complement' => null,
                ]
            ],
            'packages' => [
                [
                    'pickupIndex' => null,
                    'recipient' => [
                        'name' => null,
                        'phone' => null,
                    ],
                    'address' => [
                        'lat' => null,
                        'lng' => null,
                        'address' => null,
                        'complement' => null,
                    ],
                    'charge' => [
                        'value' => null,
                        'method' => null,
                        'change' => null,
                    ],
                    'dimensions' => [
                        'width' => null,
                        'height' => null,
                        'length' => null,
                    ]
                ]
            ]
        ];

        $input = array_merge($input, $data);

        $query = GraphQL::createObject(
            'query',
            'estimateCreateOrder',
            [
                'totalEstimate' => [
                    'totalCost',
                    'totalEta',
                    'totalDistance',
                ],
                'ordersEstimate' => [
                    'packages' => [
                        'isReturn',
                        'cost',
                        'eta',
                        'outOfCoverageArea',
                        'outOfCityCover',
                        'originalIndex',
                        'resolvedAddress',
                        'originalIndex',
                    ],
                    'optimized' => [
                        'cost',
                        'eta',
                        'distance',
                    ],
                ],
                'packagesWithErrors' => [
                    'originalIndex',
                    'error',
                    'resolvedAddress',
                ],
            ],
            $input
        );

        return new Request('post', '/', $query);
    }

    public function estimateFixedLocal(array $data)
    {
        $input = [
            'shopId' => null,
            'packagesDestination' => [
                'lat' => null,
                'lng' => null,
            ],
            'chargeMethod' => 64,
            'optimize' => true,
        ];

        $input = array_merge($input, $data);

        $query = GraphQL::createObject(
            'query',
            'estimate',
            [
                'packages' => [
                    'error',
                    'eta',
                    'index',
                    'rideCm',
                    'outOfCityCover',
                    'outOfCoverageArea',
                    'originalIndex',
                    'waypoint' => [
                        'indexDisplay',
                        'originalIndexDisplay',
                        'role',
                    ],
                ],
                'routeOptimized',
                'normal' => [
                    'cost',
                    'distance',
                    'eta',
                ],
                'optimized' => [
                    'cost',
                    'distance',
                    'eta',
                ],
            ],
            $input
        );

        return new Request('post', '/', $query);
    }

    public function retrieveOrderAndTrackDelivery(int $orderPk)
    {
        $query = sprintf('
            query {
              retrieveOrderWithPk(orderPk: %d) {
                status
                statusDisplay
                originalEta
                totalTime
                pricing {
                  totalCm
                }
                packages {
                  pk
                  shareds {
                    edges {
                      node {
                        trackingUrl
                      }
                    }
                  }
                }
                currentDriverPosition {
                  lat
                  lng
                  currentWaypointIndex
                  currentWaypointIndexDisplay
                }
              }
            }', $orderPk);

        return new Request('post', '/', $query);
    }

    public function retrieveOrdersWithTrackingKey(string $trackingKey)
    {
        $query = sprintf('
            query {
              retrieveOrdersWithTrackingKey(trackingKey: "%s") {
                status
                statusDisplay
                originalEta
                totalTime
                pricing {
                  totalCm
                }
                    packages {
                  pk
                        shareds {
                            edges {

                                node {
                                    trackingUrl
                                }
                            }
                        }
                    }
                currentDriverPosition {
                  lat
                  lng
                  currentWaypointIndex
                  currentWaypointIndexDisplay
                }
              }
            }', $trackingKey);

        return new Request('post', '/', $query);
    }
}
