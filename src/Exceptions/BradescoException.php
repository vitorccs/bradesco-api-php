<?php
namespace BradescoApi\Exceptions;

use Exception;

class BradescoException extends Exception
{
    protected $errorCode;

    public function __construct(string $message = null, $errorCode = null)
    {
        $message = $message ? trim($message) : 'Undefined error';

        $this->errorCode = $errorCode;

        parent::__construct($message);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
