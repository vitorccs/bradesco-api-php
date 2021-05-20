<?php

namespace BradescoApi\Exceptions;

use Exception;

class BradescoException extends Exception
{
    /**
     * @var int|null
     */
    protected $errorCode;

    /**
     * @param string|null $message
     * @param int|null $errorCode
     */
    public function __construct(string $message = null, int $errorCode = null)
    {
        $message = $message ? trim($message) : 'Undefined error';

        $this->errorCode = $errorCode;

        parent::__construct($message);
    }

    /**
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }
}
