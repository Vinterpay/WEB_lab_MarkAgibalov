<<<<<<< HEAD
﻿<?php
namespace App\Helpers;

use GuzzleHttp\Client;

class ClientFactory
{
    public static function make(string $baseUri, array $options = []): Client
    {
        $defaultOptions = [
            'base_uri' => $baseUri,
            'timeout'  => 10.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ];
        return new Client(array_merge($defaultOptions, $options));
    }

    public static function makeRedis(): Client
    {
        // Для демонстрации используем HTTP-интерфейс (Redis REST API), если доступен, иначе эмуляция
        // В реальном проекте - подключение через phpredis
        return self::make('http://redis:6379/', [
            'timeout' => 5.0
        ]);
    }

    public static function makeElastic(): Client
    {
        return self::make('http://elasticsearch:9200/');
    }

    public static function makeClickHouse(): Client
    {
        return self::make('http://clickhouse:8123/', [
            'headers' => [
                'Content-Type' => 'text/plain',
            ]
        ]);
    }
}
?>
=======
<?php
namespace App\Helpers;
use GuzzleHttp\Client;
class ClientFactory {
    public static function makeElastic(): Client {
        return new Client(['base_uri' => 'http://elasticsearch:9200/', 'timeout' => 10]);
    }
    public static function makeClickHouse(): Client {
        return new Client(['base_uri' => 'http://clickhouse:8123/', 'headers' => ['Content-Type' => 'text/plain']]);
    }
}
?>
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
