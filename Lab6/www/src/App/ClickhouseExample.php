<?php
namespace App;
use App\Helpers\ClientFactory;
class ClickhouseExample {
    private $client;
    public function __construct() {
        $this->client = ClientFactory::make('http://lab6_clickhouse:8123/');
    }
    public function execute(string $sql): string {
        $response = $this->client->post('', [
            'body' => $sql,
            'headers' => ['Content-Type' => 'text/plain']
        ]);
        return $response->getBody()->getContents();
    }
}
