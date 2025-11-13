<<<<<<< HEAD
﻿<?php
namespace App\Services;

use App\Helpers\ClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ClickHouseService
{
    private Client $client;
    private string $database = 'student_db';
    private string $table = 'registrations_analytics';

    public function __construct()
    {
        $this->client = ClientFactory::makeClickHouse();
        $this->ensureDatabaseExists();
        $this->ensureTableExists();
    }

    private function ensureDatabaseExists(): void
    {
        try {
            $this->executeQuery("CREATE DATABASE IF NOT EXISTS {$this->database}");
        } catch (\Exception $e) {
            error_log("ClickHouse database creation error: " . $e->getMessage());
        }
    }

    private function ensureTableExists(): void
    {
        $createTable = "
            CREATE TABLE IF NOT EXISTS {$this->database}.{$this->table} (
                registration_id String,
                full_name String,
                email String,
                faculty String,
                course String,
                group_name String,
                registration_date DateTime,
                duration_seconds UInt32,
                status String,
                created_at DateTime DEFAULT now()
            ) ENGINE = MergeTree()
            PARTITION BY toYYYYMM(registration_date)
            ORDER BY (registration_date, faculty)
        ";
        try {
            $this->executeQuery($createTable);
        } catch (\Exception $e) {
            error_log("ClickHouse table creation error: " . $e->getMessage());
        }
    }

    public function executeQuery(string $query): array
    {
        try {
            $response = $this->client->post('', [
                'body' => $query,
                'query' => [
                    'database' => $this->database
                ]
            ]);
            $result = $response->getBody()->getContents();
            return $this->parseResult($result);
        } catch (RequestException $e) {
            error_log("ClickHouse query error: " . $e->getMessage());
            return [];
        }
    }

    public function insertRegistrationAnalytics(array $data): bool
    {
        $query = "
            INSERT INTO {$this->database}.{$this->table} 
            (registration_id, full_name, email, faculty, course, group_name, registration_date, duration_seconds, status)
            VALUES
        ";
        $values = [
            $data['id'] ?? uniqid(),
            $data['full_name'] ?? '',
            $data['email'] ?? '',
            $data['faculty'] ?? '',
            $data['course'] ?? '',
            $data['group_name'] ?? '',
            $data['registration_date'] ?? date('Y-m-d H:i:s'),
            $data['duration_seconds'] ?? 60,
            $data['status'] ?? 'completed'
        ];
        $formattedValues = "('" . implode("', '", array_map(function($value) {
            return addslashes($value);
        }, $values)) . "')";
        try {
            $this->executeQuery($query . $formattedValues);
            return true;
        } catch (\Exception $e) {
            error_log("ClickHouse insert error: " . $e->getMessage());
            return false;
        }
    }

    public function getDailyRegistrations(): array
    {
        $query = "
            SELECT 
                toDate(registration_date) as date,
                count(*) as registrations_count,
                avg(duration_seconds) as avg_duration,
                sum(case when status = 'completed' then 1 else 0 end) as completed_count
            FROM {$this->database}.{$this->table}
            WHERE registration_date >= today() - interval 30 day
            GROUP BY date
            ORDER BY date DESC
        ";
        return $this->executeQuery($query);
    }

    public function getFacultyPerformance(): array
    {
        $query = "
            SELECT 
                faculty,
                count(*) as registrations_count,
                avg(duration_seconds) as avg_duration,
                count(group_name) as unique_groups
            FROM {$this->database}.{$this->table}
            WHERE registration_date >= today() - interval 30 day
            GROUP BY faculty
            ORDER BY registrations_count DESC
        ";
        return $this->executeQuery($query);
    }

    public function getCourseTrends(): array
    {
        $query = "
            SELECT 
                course,
                toWeek(registration_date) as week_number,
                count(*) as registrations_count
            FROM {$this->database}.{$this->table}
            WHERE registration_date >= today() - interval 90 day
            GROUP BY course, week_number
            ORDER BY week_number DESC, registrations_count DESC
        ";
        return $this->executeQuery($query);
    }

    private function parseResult(string $result): array
    {
        $lines = explode("
", trim($result));
        $data = [];
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $data[] = explode("\t", $line);
            }
        }
        return $data;
    }
}
?>
=======
<?php
namespace App\Services;
use App\Helpers\ClientFactory;
class ClickHouseService {
    private $client;
    public function __construct() {
        $this->client = ClientFactory::makeClickHouse();
        $this->client->post('', ['body' => "CREATE DATABASE IF NOT EXISTS university"]);
        $this->client->post('', ['body' => "
            CREATE TABLE IF NOT EXISTS university.students (
                id String,
                full_name String,
                faculty String,
                course String,
                registration_date DateTime
            ) ENGINE = MergeTree() ORDER BY registration_date
        "]);
    }
    public function insert($student) {
        $vals = "('" . implode("', '", [
            addslashes($student['id']),
            addslashes($student['full_name']),
            addslashes($student['faculty']),
            addslashes($student['course']),
            date('Y-m-d H:i:s')
        ]) . "')";
        $this->client->post('', ['body' => "INSERT INTO university.students VALUES $vals"]);
    }
    public function getFacultyStats() {
        $res = $this->client->post('', ['body' => "
            SELECT faculty, count(*) as cnt
            FROM university.students
            GROUP BY faculty
            ORDER BY cnt DESC
        "]);
        return explode("\n", trim($res->getBody()));
    }
}
?>
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
