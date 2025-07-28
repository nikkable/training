<?php

namespace App\Services\Wilberries;

use App\Services\Wilberries\Contracts\ParserInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WilberriesParser implements ParserInterface
{
    private Client $httpClient;
    private array $products = [];

    public function __construct()
    {
        $this->httpClient = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    /**
     * Парсинг данных с указанного URL
     * @param string $url
     * @return array
     * @throws GuzzleException
     */
    public function parse(string $url): array
    {
        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            $this->processData($data);

            return $this->products;
        } catch (GuzzleException $e) {
            throw new \Exception('Ошибка при получении данных: ' . $e->getMessage());
        }
    }

    /**
     * Обработка полученных данных
     * @param array $data
     */
    private function processData(array $data): void
    {
        if (!isset($data['products']) || !is_array($data['products'])) {
            throw new \Exception('Неверная структура данных: отсутствует массив products');
        }

        $this->products = [];
         foreach ($data['products'] as $productData) {
             try {
                 $product = new Product(
                     (string)($productData['id'] ?? ''),
                     (string)($productData['name'] ?? ''),
                     (string)($productData['brand'] ?? ''),
                 );
                 $this->products[] = $productData['name'];
             } catch (\Exception $e) {
                 continue;
             }
         }
    }

    /**
     * Получение списка продуктов
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}
