<?php
namespace App;
use App\Helpers\ClientFactory;
class RedisExample {
    private $client;
    public function __construct() {
        $this->client = ClientFactory::make('http://lab6_redis_cmd:8081/');
    }
    public function setValue(string $key, string $value): bool {
        $response = $this->client->get("api/key/local/$key", [
            'query' => ['action' => 'set', 'value' => $value]
        ]);
        return $response->getStatusCode() === 200;
    }
    public function getValue(string $key): ?string {
        $response = $this->client->get("api/key/local/$key");
        return ($response->getStatusCode() === 200) 
            ? json_decode($response->getBody(), true)['value'] ?? null
            : null;
    }
}
