<?php

namespace CodersFree\LaravelGreenter\DTOs;

class ErrorDto
{
    public function __construct(
        private ?string $code = null,
        private ?string $message = null,
    ) {
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return array_filter([
            'code' => $this->code,
            'message' => $this->message,
        ], fn ($value) => !is_null($value));
    }
}