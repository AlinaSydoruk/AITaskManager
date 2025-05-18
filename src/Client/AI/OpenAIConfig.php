<?php

namespace App\Client\AI;

readonly class  OpenAIConfig
{
    public function __construct(
        private  string $key,
        private  string $organization,
        private  string $model,
    )
    {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getOrganization(): string
    {
        return $this->organization;
    }

    public function getModel(): string
    {
        return $this->model;
    }

}