<?php

namespace App\Core;

/**
 * Generic response
 */

class Response {

    /**
     * @param int $httpCode response code
     * @param string $type success/error
     * @param array|string $message
     */
    function __construct(private int $httpCode, private string $type, private array|string $message) {
    }

    public function getHttpCode(): int {
        return $this->httpCode;
    }

    public function getData(): array {
        return [
            'type' => $this->type,
            'msg' => $this->message
        ];
    }
}
