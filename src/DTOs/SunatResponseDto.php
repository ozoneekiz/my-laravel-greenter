<?php

namespace CodersFree\LaravelGreenter\DTOs;

class SunatResponseDto
{
    public function __construct(
        private ?bool $success = null,
        private ?ErrorDto $error = null,
        private ?string $cdrZip = null,
        private ?CdrResponseDto $cdrResponse = null,
    ) {
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }

    public function getError(): ?ErrorDto
    {
        return $this->error;
    }

    public function getCdrZip(): ?string
    {
        return $this->cdrZip;
    }

    public function getCdrResponse(): ?CdrResponseDto
    {
        return $this->cdrResponse;
    }

    public function toArray(): array
    {
        return array_filter([
            'success' => $this->success,
            'error' => $this->error?->toArray(),
            /* 'cdr_zip' => $this->cdrZip, */
            'cdr_zip' => $this->cdrZip 
                ? base64_encode($this->cdrZip) 
                : null,
            'cdr_response' => $this->cdrResponse?->toArray(),
        ], fn ($value) => !is_null($value));
    }
    
}