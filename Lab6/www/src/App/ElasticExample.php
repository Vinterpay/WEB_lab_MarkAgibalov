<?php
namespace App;
use App\Helpers\ClientFactory;
class ElasticExample {
    private $client;
    public function __construct() {
        $this->client = ClientFactory::make('http://lab6_elastic:9200/');
    }
    public function indexDocument(string $index, string $id, array $data): bool {
        $response = $this->client->put("$index/_doc/$id", [
            'json' => $data,
            'headers' => ['Content-Type' => 'application/json']
        ]);
        return $response->getStatusCode() === 201;
    }
    public function search(string $index, string $field, string $value): array {
        $body = ['query' => ['match' => [$field => $value]]];
        $response = $this->client->get("$index/_search", [
            'json' => $body,
            'headers' => ['Content-Type' => 'application/json']
        ]);
        return ($response->getStatusCode() === 200)
            ? json_decode($response->getBody(), true)
            : [];
    }
}
