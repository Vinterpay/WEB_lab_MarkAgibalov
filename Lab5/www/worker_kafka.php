<?php
// Добавляем обработку ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Функция для логирования
function logWorkerMessage($message, $type = 'INFO') {
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message" . PHP_EOL;
    
    file_put_contents($logDir . '/worker.log', $logEntry, FILE_APPEND | LOCK_EX);
    echo $logEntry;
}

try {
    // Проверяем существование необходимых файлов
    if (!file_exists('KafkaManager.php')) {
        throw new Exception('Файл KafkaManager.php не найден');
    }
    
    if (!file_exists('Student.php')) {
        throw new Exception('Файл Student.php не найден');
    }
    
    require_once 'KafkaManager.php';
    require_once 'Student.php';

    logWorkerMessage('🚀 Kafka Worker для обработки регистраций студентов запущен...');
    logWorkerMessage('📊 Топик: student_registrations');
    logWorkerMessage('👥 Группа: student_registration_group');
    logWorkerMessage('⏳ Ожидание сообщений...');

    $kafkaManager = new KafkaManager();

    // Функция обработки сообщений
    function processStudentRegistration($data) {
        logWorkerMessage("📥 Получено сообщение из Kafka: " . json_encode($data, JSON_UNESCAPED_UNICODE));
        
        try {
            // Подключаемся к базе данных
            $pdo = new PDO('mysql:host=db;dbname=student_db', 'student_user', 'student_pass');
            $pdo->exec("SET NAMES 'utf8mb4'");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $student = new Student($pdo);
            
            // Имитация длительной обработки
            logWorkerMessage("⏳ Обработка данных студента: " . $data['full_name']);
            sleep(1); // Уменьшаем время обработки для демонстрации
            
            // Дополнительная логика обработки
            $processingData = [
                'student_id' => $data['student_id'],
                'full_name' => $data['full_name'],
                'faculty' => $data['faculty'],
                'processed_at' => date('Y-m-d H:i:s')
            ];
            
            logWorkerMessage("✅ Регистрация студента " . $data['full_name'] . " обработана успешно");
            
            // Сохраняем результат обработки
            $logDir = __DIR__ . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            file_put_contents(
                $logDir . '/processed_students.log', 
                json_encode($processingData, JSON_UNESCAPED_UNICODE) . PHP_EOL, 
                FILE_APPEND | LOCK_EX
            );
            
        } catch (Exception $e) {
            logWorkerMessage("❌ Ошибка при обработке: " . $e->getMessage(), 'ERROR');
            
            $errorData = [
                'error' => $e->getMessage(),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $logDir = __DIR__ . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            file_put_contents(
                $logDir . '/processing_errors.log', 
                json_encode($errorData, JSON_UNESCAPED_UNICODE) . PHP_EOL, 
                FILE_APPEND | LOCK_EX
            );
        }
        
        echo "---" . PHP_EOL;
    }

    // Запускаем consumer
    $kafkaManager->consume('processStudentRegistration');
    
} catch (Exception $e) {
    logWorkerMessage("💥 Критическая ошибка при запуске worker: " . $e->getMessage(), 'CRITICAL');
    exit(1);
}
?>
