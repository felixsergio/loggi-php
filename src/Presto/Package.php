<?php

namespace FelixLoggi\Presto;

use FelixLoggi\Request;
use FelixLoggi\GraphQL;

class Package
{
    public function all(int $shopId)
    {
        $query = sprintf('
            query {
              allPackages(shopId: %d) {
                edges {
                  node {
                    pk
                    status
                    orderId
                    orderStatus
                  }
                }
              }
            }', $shopId);

        return new Request('post', '/', $query);
    }

    public function history(int $packageId)
    {
        $query = sprintf('
            query {
              packageHistory(packageId: %d) {
                signatureUrl
                signedByName
                statuses {
                  status
                  statusDisplay
                  detailedStatusDisplay
                  statusCode
                  updated
                }
              }
            }', $packageId);

        return new Request('post', '/', $query);
    }

    public function status(int $packageId)
    {
        $query = sprintf('
            query {
              packageOrder(packageId: %d) {
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
            }', $packageId);

        return new Request('post', '/', $query);
    }
}
