<?php

namespace App\Services\Ai\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class WAFunction
{
    public function __construct(
        public string $name,
        public string $description,
        public string $permission = ''
    ) {}
}
