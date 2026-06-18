<?php

namespace App\Services\Ai\Attributes;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class WAFunctionParam
{
    public function __construct(
        public string $name,
        public string $type,
        public string $description,
        public array $enum = [],
        public bool $required = false
    ) {}
}
