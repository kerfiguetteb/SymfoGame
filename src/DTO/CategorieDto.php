<?php

namespace App\DTO;

class CategorieDto 
{
        public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $description,
        public ?string $imageUrl,
        public array $produits,
    ) {}

}