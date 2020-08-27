<?php

namespace FelixLoggi;

use FelixLoggi\GraphQL\ObjectGraphQL;

class GraphQL
{
    /**
     * @var array
     */
    private $value = [];

    /**
     * Query constructor.
     * @param array $value
     */
    function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->parse($this->value);
    }

    /**
     * @param array $array
     * @return string
     */
    private function parse($graphql)
    {

        $query = sprintf('{
            "query" : "%s"
        }', addslashes($graphql));

        $query = trim(preg_replace('/\s+/', ' ', $query));

        return $query;
    }

    /**
     * @param string $schema mutation | query
     * @param string $object
     * @param array $return
     * @param array $args
     * @return string
     */
    public static function createObject(string $schema, string $object, array $return, array $args = [])
    {
        $object = new ObjectGraphQL($object, $return, $args);
        return $schema == 'mutation' ? $object->getMutation() :  $object->getQuery();
    }
}
