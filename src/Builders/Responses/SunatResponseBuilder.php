<?php

namespace CodersFree\LaravelGreenter\Builders\Responses;

use CodersFree\LaravelGreenter\DTOs\CdrResponseDto;
use CodersFree\LaravelGreenter\DTOs\ErrorDto;
use CodersFree\LaravelGreenter\DTOs\SunatResponseDto;
use Greenter\Model\Response\CdrResponse;

class SunatResponseBuilder
{
    private ?bool $success = null;
    private ?ErrorDto $error = null;
    private ?string $cdrZip = null;
    private ?CdrResponseDto $cdrResponse = null;

    public static function make(): self
    {
        return new self();
    }

    public function success(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function error(ErrorDto $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function cdrZip(?string $cdrZip): self
    {
        $this->cdrZip = $cdrZip;
        return $this;
    }

    public function cdrResponse(CdrResponse $cdr): self
    {
        $code = (int)$cdr->getCode();

        $this->cdrResponse = CdrResponseBuilder::make()
            ->accepted($code === 0)
            ->id($cdr->getId())
            ->code($cdr->getCode())
            ->description($cdr->getDescription())
            ->notes($cdr->getNotes())
            ->build();

        return $this;
    }

    public function build(): SunatResponseDto
    {
        return new SunatResponseDto(
            success: $this->success,
            error: $this->error,
            cdrZip: $this->cdrZip,
            cdrResponse: $this->cdrResponse,
        );
    }
}