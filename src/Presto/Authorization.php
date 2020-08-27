<?php

namespace FelixLoggi\Presto;

use FelixLoggi\Request;

class Authorization
{
    public function login(string $login, string $password)
    {
        $query = sprintf('mutation {
                login(
                    input: {
                        email: \"%s\",
                        password: \"%s\" }
                ) {
                    user {
                        apiKey
                    }
                }
            }', $login, $password);

        $request = new Request('post', '/graphql', $query);

        return $request;
    }

}
