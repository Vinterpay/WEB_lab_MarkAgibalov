<?php
// www/worker_kafka_student.php
// Этот скрипт должен запускаться вручную или через процесс-менеджер (supervisor)
// Он подключается к топику Kafka и обрабатывает сообщения о регистрации студентов

// Проверяем, установлено ли расширение rdkafka
if (!extension_loaded('rdkafka')) {
    die("❌ Расширение php-rdkafka не установлено. Проверьте Dockerfile контейнера lab5_php.
");
}

use RdKafka\Consumer;
use RdKafka\Conf;

echo "👷 Рабочий (Consumer) запущен для 'Регистрации студента' (Kafka, Вариант 1)...
";

// Конфигурация
$conf = new Conf();
$conf->set('group.id', 'student_registration_group');
$conf->set('metadata.broker.list', 'lab5_kafka:9092'); // Имя сервиса Kafka из docker-compose.yml
// Дополнительные настройки
$conf->set('enable.auto.commit', 'true');
$conf->set('auto.offset.reset', 'earliest'); // earliest или latest

$consumer = new Consumer($conf);
$topicName = 'student_registrations'; // Топик для варианта 1
$partition = 0;

$topic = $consumer->newTopic($topicName);
$topic->consumeStart($partition, RD_KAFKA_OFFSET_STORED);

while (true) {
    $message = $topic->consume($partition, 1000); // Таймаут 1 секунда
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            // Успешное сообщение
            echo "📥 Получена регистрация: " . $message->payload . "
";
            $data = json_decode($message->payload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Обработка данных (например, сохранение в БД, отправка email/SMS, обновление статуса и т.д.)
                // Имитация обработки (например, запись в лог)
                echo "   Обработка данных студента: " . $data['full_name'] . " (ID: " . ($data['id'] ?? 'N/A') . ")
";
                // Имитация длительной операции
                sleep(2);
                $logEntry = date('Y-m-d H:i:s') . " - Processed student registration: " . $message->payload . "
";
                file_put_contents(__DIR__ . '/worker_kafka_student_processing.log', $logEntry, FILE_APPEND | LOCK_EX);
                echo "   ✅ Обработано.
";
            } else {
                echo "   ❌ Ошибка JSON в сообщении: " . $message->payload . "
";
                error_log("Kafka consumer JSON error: " . $message->payload);
            }
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            // Достигли конца партиции, продолжаем ожидать
            // echo "   (Достигнут конец партиции)
";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            // Таймаут ожидания сообщения, продолжаем цикл
            // echo "   (Таймаут ожидания)
";
            break;
        default:
            // Другая ошибка
            echo "❌ Ошибка при получении сообщения: " . $message->errstr() . "
";
            error_log("Kafka consumer error: " . $message->errstr());
            break;
    }
}

$topic->consumeStop($partition);
$consumer->close();
echo "🚪 Рабочий завершил работу.
";
?>
