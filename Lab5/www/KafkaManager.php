<?php
require_once 'vendor/autoload.php';

use Kafka\Producer;
use Kafka\ProducerConfig;
use Kafka\Consumer;
use Kafka\ConsumerConfig;

class KafkaManager {
    private $topic = 'student_registrations';

    public function __construct() {
        // Проверяем доступность Kafka
        $this->checkKafkaConnection();
    }

    private function checkKafkaConnection() {
        // Простая проверка доступности Kafka
        $connected = false;
        $maxAttempts = 10;
        $attempt = 0;
        
        while (!$connected && $attempt < $maxAttempts) {
            try {
                $testConfig = ProducerConfig::getInstance();
                $testConfig->setMetadataBrokerList('kafka:9092');
                $connected = true;
            } catch (Exception $e) {
                $attempt++;
                if ($attempt < $maxAttempts) {
                    sleep(2); // Ждем 2 секунды перед следующей попыткой
                }
            }
        }
        
        if (!$connected) {
            throw new Exception('Не удалось подключиться к Kafka после ' . $maxAttempts . ' попыток');
        }
    }

    public function publish($data) {
        try {
            $config = ProducerConfig::getInstance();
            $config->setMetadataBrokerList('kafka:9092');
            $config->setRequiredAck(1);
            $config->setIsAsyn(false);
            $config->setRequestTimeoutMs(10000); // 10 секунд таймаут

            $producer = new Producer(function() use ($data) {
                return [
                    [
                        'topic' => $this->topic,
                        'value' => json_encode($data, JSON_UNESCAPED_UNICODE),
                        'key' => 'student_registration_' . uniqid(),
                    ]
                ];
            });

            $result = $producer->send(true);
            
            if ($result === false) {
                throw new Exception('Ошибка отправки сообщения в Kafka');
            }
            
            // Логируем успешную отправку
            $this->logMessage('producer', 'SUCCESS', $data);
            
            return true;
            
        } catch (Exception $e) {
            $this->logMessage('producer', 'ERROR', $data, $e->getMessage());
            throw $e;
        }
    }

    public function consume(callable $callback) {
        try {
            $config = ConsumerConfig::getInstance();
            $config->setMetadataBrokerList('kafka:9092');
            $config->setGroupId('student_registration_group');
            $config->setTopics([$this->topic]);
            $config->setOffsetReset('earliest');
            $config->setSessionTimeoutMs(30000);

            $consumer = new Consumer();
            
            echo "👷 Kafka Consumer запущен для топика: {$this->topic}" . PHP_EOL;
            echo "⏳ Ожидание сообщений..." . PHP_EOL . PHP_EOL;
            
            $consumer->start(function($topic, $part, $message) use ($callback) {
                if (isset($message['message']['value'])) {
                    $data = json_decode($message['message']['value'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $callback($data);
                    } else {
                        echo "❌ Ошибка декодирования JSON: " . json_last_error_msg() . PHP_EOL;
                        $this->logMessage('consumer', 'JSON_ERROR', $message['message']['value'], json_last_error_msg());
                    }
                }
            });
            
        } catch (Exception $e) {
            echo "❌ Ошибка в consumer: " . $e->getMessage() . PHP_EOL;
            $this->logMessage('consumer', 'ERROR', null, $e->getMessage());
            sleep(10); // Увеличиваем паузу перед перезапуском
            $this->consume($callback);
        }
    }

    private function logMessage($type, $status, $data = null, $error = null) {
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'status' => $status,
            'data' => $data,
            'error' => $error
        ];
        
        $logFile = $logDir . '/kafka_' . $type . '.log';
        file_put_contents($logFile, json_encode($logEntry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function getTopic() {
        return $this->topic;
    }
}
?>
