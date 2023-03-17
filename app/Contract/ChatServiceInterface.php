<?php

namespace App\Contract;

interface ChatServiceInterface
{
    public function getInitContext(): array;

    public function chat(array $context, float $temperature): array;
}