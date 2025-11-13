<<<<<<< HEAD
﻿<?php
namespace App\Services;

use App\Helpers\ClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RedisService
{
    private Client $client;
    private string $prefix = 'student:';

    public function __construct()
    {
        // Эмуляция Redis через файловую систему для надежности
        $this->client = ClientFactory::makeRedis();
    }

    private function getStorageFile(): string
    {
        return '/tmp/redis_student_data.json';
    }

    private function loadData(): array
    {
        $file = $this->getStorageFile();
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        return $content ? json_decode($content, true) : [];
    }

    private function saveData(array $data): void
    {
        $file = $this->getStorageFile();
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function cacheStudent(array $student): bool
    {
        try {
            $data = $this->loadData();
            $key = $this->prefix . 'student:' . ($student['id'] ?? uniqid());
            $data[$key] = [
                'value' => $student,
                'timestamp' => time(),
                'expires' => time() + 3600 // 1 час
            ];
            $this->saveData($data);
            return true;
        } catch (\Exception $e) {
            error_log("Redis cache error: " . $e->getMessage());
            return false;
        }
    }

    public function getCachedStudent(string $id): ?array
    {
        try {
            $data = $this->loadData();
            $key = $this->prefix . 'student:' . $id;
            if (isset($data[$key])) {
                $cached = $data[$key];
                if ($cached['expires'] > time()) {
                    return $cached['value'];
                }
                unset($data[$key]);
                $this->saveData($data);
            }
            return null;
        } catch (\Exception $e) {
            error_log("Redis get error: " . $e->getMessage());
            return null;
        }
    }

    public function cacheStats(array $stats): bool
    {
        try {
            $data = $this->loadData();
            $key = $this->prefix . 'stats:overview';
            $data[$key] = [
                'value' => $stats,
                'timestamp' => time(),
                'expires' => time() + 300 // 5 минут
            ];
            $this->saveData($data);
            return true;
        } catch (\Exception $e) {
            error_log("Redis stats cache error: " . $e->getMessage());
            return false;
        }
    }

    public function getCachedStats(): ?array
    {
        try {
            $data = $this->loadData();
            $key = $this->prefix . 'stats:overview';
            if (isset($data[$key])) {
                $cached = $data[$key];
                if ($cached['expires'] > time()) {
                    return $cached['value'];
                }
            }
            return null;
        } catch (\Exception $e) {
            error_log("Redis stats get error: " . $e->getMessage());
            return null;
        }
    }

    public function incrementCounter(): int
    {
        try {
            $data = $this->loadData();
            $key = $this->prefix . 'counter:registrations';
            $count = ($data[$key]['value'] ?? 0) + 1;
            $data[$key] = [
                'value' => $count,
                'timestamp' => time()
            ];
            $this->saveData($data);
            return $count;
        } catch (\Exception $e) {
            error_log("Redis counter error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
=======
<?php
namespace App\Services;
class RedisService {
    private function getCacheFile() { return '/tmp/student_cache.json'; }
    private function load() { return file_exists($this->getCacheFile()) ? json_decode(file_get_contents($this->getCacheFile()), true) : []; }
    private function save($data) { file_put_contents($this->getCacheFile(), json_encode($data, JSON_PRETTY_PRINT)); }
    public function cacheStudent($student) {
        $data = $this->load();
        $key = "student:{$student['id']}";
        $data[$key] = ['value' => $student, 'expires' => time() + 3600];
        $this->save($data);
        return true;
    }
    public function incrementCounter() {
        $data = $this->load();
        $key = "counter:students";
        $count = ($data[$key]['value'] ?? 0) + 1;
        $data[$key] = ['value' => $count];
        $this->save($data);
        return $count;
    }
}
?>
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
