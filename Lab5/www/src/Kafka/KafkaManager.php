<?php
// www/src/Kafka/KafkaManager.php
namespace App\Kafka;

// Используем библиотеку nmred/kafka-php
// ВНИМАНИЕ: Эта библиотека использует librdkafka под капотом (php-rdkafka extension).
// Composer установит только PHP-обёртку. Само расширение php-rdkafka нужно установить в Dockerfile PHP.
// Это делается в предыдущем шаге скрипта.

use Nmred\Kafka\Config\Config;
use Nmred\Kafka\Producer\Producer;

class KafkaManager {
    private $topic = 'student_registrations'; // Имя топика для варианта 1
    private $broker = 'lab5_kafka:9092'; // Имя сервиса Kafka из docker-compose.yml

    public function __construct() {
        // Устанавливаем конфигурацию
        Config::setBrokers($this->broker);
        Config::setTopic($this->topic);
    }

    public function publish($data) {
        try {
            // Преобразуем данные в JSON
            $msgBody = json_encode($data, JSON_UNESCAPED_UNICODE);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Ошибка JSON при публикации: ' . json_last_error_msg());
            }

            // Создаём продюсера и публикуем
            $producer = new Producer();
            $producer->setTopic($this->topic);
            $producer->send($msgBody);

            error_log("Сообщение отправлено в топик Kafka (Вариант 1): " . $msgBody);
        } catch (\Nmred\Kafka\Exception\Exception $e) {
            // Ошибка библиотеки Kafka
            error_log("Kafka publish error (lib): " . $e->getMessage());
            throw $e; // Пробросим исключение, чтобы вызывающий код мог обработать ошибку
        } catch (\Exception $e) {
            // Любая другая ошибка
            error_log("Kafka publish error (general): " . $e->getMessage());
            throw $e;
        }
    }

    // NOTE: nmred/kafka-php не предоставляет простого Consumer API.
    // Для потребления сообщений из топика обычно используется `kafka-console-consumer` или `php-rdkafka` напрямую.
    // Но для демонстрации можно создать скрипт consumer, который будет использовать `rdkafka` напрямую.
    // Мы создадим отдельный worker.php, который будет использовать php-rdkafka напрямую.
    // Этот метод будет пустым или использовать эмуляцию, если php-rdkafka не установлен.
    public function consume(callable $callback) {
        // Эмуляция или вызов внешнего скрипта
        // В реальном проекте тут будет код с использованием RdKafka\Consumer
        error_log("Метод consume не реализован для nmred/kafka-php. Используйте php-rdkafka напрямую в worker.php.");
        // Пока что просто вызовем callback с фиктивными данными для демонстрации
        // $callback(['id' => 1, 'full_name' => 'Тестовый Студент', 'faculty' => 'Компьютерные науки']);
    }
}
?>
