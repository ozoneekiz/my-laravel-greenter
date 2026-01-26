<?php

namespace CodersFree\LaravelGreenter\Builders\Responses;

use CodersFree\LaravelGreenter\DTOs\CdrResponseDto;

class CdrResponseBuilder
{
    private ?bool $accepted = null;
    private ?string $id = null;
    private ?string $code = null;
    private ?string $description = null;
    private ?array $notes = null;

    public static function make(): self
    {
        return new self();
    }

    public function accepted(bool $accepted): self
    {
        $this->accepted = $accepted;
        return $this;
    }

    public function id(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function code(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function notes(array $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function build(): CdrResponseDto
    {
        return new CdrResponseDto(
            accepted: $this->accepted,
            id: $this->id,
            code: $this->code,
            description: $this->description,
            notes: $this->notes,
        );
    }
}