<?php

namespace CodersFree\LaravelGreenter\DTOs;

class CdrResponseDto
{
    /**
     * @param string[]|null $notes
     */
    public function __construct(
        private ?bool $accepted = null,
        private ?string $id = null,
        private ?string $code = null,
        private ?string $description = null,
        private ?array $notes = null,
    ) {}

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getNotes(): ?array
    {
        return $this->notes;
    }

    public function toArray(): array
    {
        return array_filter([
            'accepted' => $this->accepted,
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'notes' => $this->notes,
        ], fn ($value) => !is_null($value));
    }
}
