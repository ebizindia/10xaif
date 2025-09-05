<?php

namespace Instamojo\Exceptions;

class InvalidRequestException extends InstamojoException {
    public function __construct($errorMessage) {
        parent::__construct (null, null, $errorMessage);
    }
}