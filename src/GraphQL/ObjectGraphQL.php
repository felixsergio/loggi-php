<?php

namespace FelixLoggi\GraphQL;

class ObjectGraphQL
{
    /**
     * @var array
     */
    private $value = [];

    /**
     * Query constructor.
     * @param array $value
     */
    function __construct(string $object, array $return, array $args = [])
    {
        $this->object = [
            'name' => $object,
            'args' => $args,
            'return' => $return
        ];
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->render();
    }

    /**
     * @param array $array
     * @return string
     */
    private function render()
    {
        $args = $this->getArgs();
        $object = $this->getName();
        $return = $this->getReturn();

        $graphql = null;

        if($object && $return){
            if($args){
                $object .= '(' . $this->parseArgs($args) . ')';
            }

            $graphql = $object . $this->parseReturn($return);
        }

        return $graphql;
    }

    private function getName()
    {
        return $this->object['name'] ?? null;
    }

    private function getArgs()
    {
        return $this->object['args'] ?? null;
    }

    private function getReturn()
    {
        return $this->object['return'] ?? [];
    }

    public  function getQuery()
    {
        return 'query { ' . $this->render() . ' }';
    }

    public  function getMutation()
    {
        return 'mutation { ' . $this->render() . ' }';
    }

    private function parseReturn(array $array = [])
    {
        $return = '{ ';

        foreach ($array as $key => $value) {

            if(!is_int($key))
                $return .= $key . ' ';

            if(is_array($value))
                $value = $this->parseReturn($value);

            $return .= $value .  ' ';
        }

        $return .= ' }';

        return $return;
    }

    private function parseArgs(array $array = [])
    {
        $return = [];

        foreach ($array as $key => $value) {

            if(is_array($value)){

                if(is_int(array_key_first($value))){
                    $value = '[' . $this->parseArgs($value) . ']';
                }else{
                    $value = '{' . $this->parseArgs($value) . '}';
                }
            }else{
                // String
                if(!is_int($key) && is_string($value)){
                    $value = '"' . addslashes($value) . '"';
                }

                // Boolean
                if(!is_int($key) && is_bool($value)){
                    $value = $value? 'true' : 'false';
                }

                // Null
                if(!is_int($key) && $value === null){
                    $value = '""';
                }

                // Raw string
                if(is_int($key) && is_string($value)){
                    $value = addslashes($value);
                }

            }

            if(!is_int($key)){
                $return[] = $key . ': ' . $value;
            }else{
                $return[] = $value;
            }

        }
        return implode(', ', $return);
    }
}
