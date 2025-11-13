<<<<<<< HEAD
﻿<?php
namespace App\Services;

use App\Helpers\ClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ElasticsearchService
{
    private Client $client;
    private string $index = 'student_registrations';

    public function __construct()
    {
        $this->client = ClientFactory::makeElastic();
    }

    public function ensureIndexExists(): bool
    {
        try {
            $response = $this->client->get($this->index);
            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                return $this->createIndex();
            }
            error_log("Elasticsearch index check error: " . $e->getMessage());
            return false;
        }
    }

    private function createIndex(): bool
    {
        try {
            $mapping = [
                'mappings' => [
                    'properties' => [
                        'full_name' => ['type' => 'text'],
                        'email' => ['type' => 'keyword'],
                        'phone' => ['type' => 'keyword'],
                        'faculty' => ['type' => 'keyword'],
                        'course' => ['type' => 'keyword'],
                        'group_name' => ['type' => 'keyword'],
                        'birth_date' => ['type' => 'date'],
                        'address' => ['type' => 'text'],
                        'registration_date' => ['type' => 'date']
                    ]
                ]
            ];
            $response = $this->client->put($this->index, ['json' => $mapping]);
            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            error_log("Elasticsearch index creation error: " . $e->getMessage());
            return false;
        }
    }

    public function indexStudent(array $student): bool
    {
        try {
            $doc = [
                'full_name' => $student['full_name'] ?? '',
                'email' => $student['email'] ?? '',
                'phone' => $student['phone'] ?? '',
                'faculty' => $student['faculty'] ?? '',
                'course' => $student['course'] ?? '',
                'group_name' => $student['group_name'] ?? '',
                'birth_date' => $student['birth_date'] ?? date('c'),
                'address' => $student['address'] ?? '',
                'registration_date' => $student['registration_date'] ?? date('c')
            ];

            $id = $student['id'] ?? uniqid();
            $response = $this->client->post("{$this->index}/_doc/{$id}", ['json' => $doc]);
            return $response->getStatusCode() === 201;
        } catch (RequestException $e) {
            error_log("Elasticsearch indexing error: " . $e->getMessage());
            return false;
        }
    }

    public function searchStudents(string $query, array $filters = []): array
    {
        try {
            $searchBody = [
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ],
                'sort' => [
                    ['registration_date' => ['order' => 'desc']]
                ],
                'size' => 50
            ];

            if (!empty($query)) {
                $searchBody['query']['bool']['must'][] = [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['full_name', 'faculty', 'group_name', 'email'],
                        'fuzziness' => 'AUTO'
                    ]
                ];
            }

            foreach ($filters as $field => $value) {
                if (!empty($value)) {
                    $searchBody['query']['bool']['must'][] = [
                        'term' => [$field => $value]
                    ];
                }
            }

            if (empty($searchBody['query']['bool']['must'])) {
                $searchBody['query'] = ['match_all' => new \stdClass()];
            }

            $response = $this->client->get("{$this->index}/_search", ['json' => $searchBody]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['hits']['hits'] ?? [];
        } catch (RequestException $e) {
            error_log("Elasticsearch search error: " . $e->getMessage());
            return [];
        }
    }

    public function getStats(): array
    {
        try {
            $aggs = [
                'size' => 0,
                'aggs' => [
                    'faculties' => [
                        'terms' => ['field' => 'faculty', 'size' => 10]
                    ],
                    'courses' => [
                        'terms' => ['field' => 'course', 'size' => 10]
                    ],
                    'groups' => [
                        'terms' => ['field' => 'group_name', 'size' => 10]
                    ]
                ]
            ];
            $response = $this->client->get("{$this->index}/_search", ['json' => $aggs]);
            $data = json_decode($response->getBody()->getContents(), true);
            return [
                'faculties' => $data['aggregations']['faculties']['buckets'] ?? [],
                'courses' => $data['aggregations']['courses']['buckets'] ?? [],
                'groups' => $data['aggregations']['groups']['buckets'] ?? []
            ];
        } catch (RequestException $e) {
            error_log("Elasticsearch stats error: " . $e->getMessage());
            return [];
        }
    }
}
?>
=======
<?php
namespace App\Services;
use App\Helpers\ClientFactory;
class ElasticsearchService {
    private $client;
    private $index = 'students';
    public function __construct() { $this->client = ClientFactory::makeElastic(); }
    public function ensureIndex() {
        try { $this->client->get($this->index); } catch (\Exception $e) {
            $this->client->put($this->index, ['json' => ['mappings' => ['properties' => [
                'full_name' => ['type' => 'text'],
                'faculty' => ['type' => 'keyword'],
                'course' => ['type' => 'keyword']
            ]]]]);
        }
    }
    public function indexStudent($student) {
        $doc = [
            'full_name' => $student['full_name'],
            'faculty' => $student['faculty'],
            'course' => $student['course']
        ];
        $this->client->put("{$this->index}/_doc/{$student['id']}", ['json' => $doc]);
    }
    public function search($query, $faculty = '') {
        $body = ['query' => ['bool' => ['must' => []]]];
        if ($query) $body['query']['bool']['must'][] = ['multi_match' => ['query' => $query, 'fields' => ['full_name']]];
        if ($faculty) $body['query']['bool']['must'][] = ['term' => ['faculty' => $faculty]];
        if (empty($body['query']['bool']['must'])) $body['query'] = ['match_all' => new \stdClass()];
        $res = $this->client->get("{$this->index}/_search", ['json' => $body]);
        return json_decode($res->getBody(), true)['hits']['hits'] ?? [];
    }
}
?>
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
