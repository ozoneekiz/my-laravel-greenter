<?php

namespace CodersFree\LaravelGreenter\Builders\Responses;

use CodersFree\LaravelGreenter\DTOs\SendResultDTO;
use CodersFree\LaravelGreenter\DTOs\SunatResponseDto;
use Greenter\Model\DocumentInterface;
use Greenter\Report\XmlUtils;

class SendResultBuilder
{
    private ?DocumentInterface $document = null;
    private ?string $xml = null;
    private ?string $hash = null;

    private ?SunatResponseDto $sunatResponse = null;
    
    public static function make(): self
    {
        return new self();
    }

    public function document(DocumentInterface $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function xml(string $xml): self
    {
        $this->xml = $xml;
        $this->hash = (new XmlUtils())->getHashSign($xml);

        return $this;
    }

    public function sunatResponse(SunatResponseDto $sunatResponse): self
    {
        $this->sunatResponse = $sunatResponse;
        return $this;
    }

    public function build(): SendResultDTO
    {
        return new SendResultDTO(
            document: $this->document,
            xml: $this->xml,
            hash: $this->hash,
            sunatResponse: $this->sunatResponse,
        );
    }
    
}