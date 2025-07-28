<?php

namespace App\Services\Wilberries\Contracts;

interface ParserInterface
{
    public function parse(string $url): array;
    public function getProducts(): array;
}
