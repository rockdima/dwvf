<?php

namespace App\Core;

/**
 * Get body from the request and convert into array
 */

class Body {

    public array $data = [];

    function __construct() {
        $body = file_get_contents("php://input");

        if($body)
            $this->data = json_decode($body, true);
    }
}
