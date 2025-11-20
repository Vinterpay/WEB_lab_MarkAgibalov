<?php
require_once 'KafkaManager.php';

echo "🧪 Тестирование подключения к Kafka..." . PHP_EOL;

try {
    $kafka = new KafkaManager();
    echo "✅ Подключение к Kafka успешно" . PHP_EOL;
    
    // Тестовая отправка сообщения
    $testData = [
        'action' => 'test',
        'message' => 'Тестовое сообщение от ' . date('Y-m-d H:i:s'),
        'student_id' => 'test_' . uniqid()
    ];
    
    $result = $kafka->publish($testData);
    
    if ($result) {
        echo "✅ Тестовое сообщение отправлено в Kafka" . PHP_EOL;
        echo "📊 Топик: " . $kafka->getTopic() . PHP_EOL;
        echo "📨 Данные: " . json_encode($testData, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    } else {
        echo "❌ Не удалось отправить тестовое сообщение" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . PHP_EOL;
}
?>
