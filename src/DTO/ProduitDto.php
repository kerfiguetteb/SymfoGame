<?php

namespace App\DTO;

class ProduitDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $description,
        public ?bool $isActive,
        public ?bool $stock,
        public array $images
    ) {}
}