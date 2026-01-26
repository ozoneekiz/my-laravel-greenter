<?php

namespace CodersFree\LaravelGreenter\DTOs;

use Greenter\Model\DocumentInterface;

class SendResultDTO
{
    public function __construct(
        private ?DocumentInterface $document = null,
        private ?string $xml = null,
        private ?string $hash = null,
        private ?SunatResponseDto $sunatResponse = null,
    ) {
    }

    public function getDocument(): ?DocumentInterface
    {
        return $this->document;
    }

    public function getXml(): ?string
    {
        return $this->xml;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function getSunatResponse(): ?SunatResponseDto
    {
        return $this->sunatResponse;
    }

    public function toArray(): array
    {
        return array_filter([
            'xml' => $this->xml,
            'hash' => $this->hash,
            'sunat_response' => $this->sunatResponse?->toArray(),
        ], fn ($value) => !is_null($value));
    }
}