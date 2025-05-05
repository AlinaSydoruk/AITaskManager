<?php

namespace App\Client\AI;

class OpenAIConfig
{
    public function __construct(
        private readonly string $key,
        private readonly string $organization,
    )
    {
    }
}