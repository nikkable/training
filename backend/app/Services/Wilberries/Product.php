<?php

namespace App\Services\Wilberries;

class Product
{
    private string $id;
    private string $name;
    private string $brand;

    public function __construct(
        string $id,
        string $name,
        string $brand
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->brand = $brand;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getBrand(): string
    {
        return $this->brand;
    }
}
