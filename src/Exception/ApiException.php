<?php

declare(strict_types=1);

namespace SupportLayer\Exception;

class ApiException extends SupportLayerException
{
    private int $httpStatus;
    private string $errorCode;

    public function __construct(int $httpStatus, string $errorCode, string $message, ?\Throwable $previous = null)
    {
        $this->httpStatus = $httpStatus;
        $this->errorCode = $errorCode;
        parent::__construct($message, $httpStatus, $previous);
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
