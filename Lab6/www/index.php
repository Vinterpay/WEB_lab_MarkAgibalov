<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\RedisExample;
use App\ElasticExample;
use App\ClickhouseExample;

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🎓 Лабораторная №6 — Вариант 1</h1>";
echo "<p><strong>Тема:</strong> Работа с Redis, Elasticsearch, ClickHouse через API</p>";

echo "<h2>🔴 Redis — Кеширование данных студента</h2>";
$redis = new RedisExample();
$student = json_encode(['name' => 'Иван Иванов', 'faculty' => 'Информационные технологии']);
$redis->setValue('student:101', $student);
echo "Записано: " . htmlspecialchars($student) . "<br>";
echo "Прочитано: " . htmlspecialchars($redis->getValue('student:101')) . "<br><br>";

echo "<h2>🔍 Elasticsearch — Поиск студентов</h2>";
$elastic = new ElasticExample();
$elastic->indexDocument('students', '101', [
    'name' => 'Иван Иванов',
    'faculty' => 'Информационные технологии'
]);
$result = $elastic->search('students', 'faculty', 'Информационные технологии');
$hits = $result['hits']['hits'] ?? [];
echo "Найдено: " . count($hits) . " студент(ов)<br><br>";

echo "<h2>⚡ ClickHouse — Аналитика</h2>";
$click = new ClickhouseExample();
$click->execute("CREATE DATABASE IF NOT EXISTS university");
$click->execute("DROP TABLE IF EXISTS university.students");
$click->execute("
    CREATE TABLE university.students (
        id UInt32,
        name String,
        faculty String
    ) ENGINE = MergeTree() ORDER BY id
");
$click->execute("INSERT INTO university.students VALUES (101, 'Иван Иванов', 'Информационные технологии')");
$count = trim($click->execute("SELECT count(*) FROM university.students"));
echo "Записей в ClickHouse: $count<br>";

echo "<hr><p>✅ Все сервисы настроены! Проверьте также:</p>";
echo "<ul>
  <li><a href='/'>Эта страница</a></li>
  <li><a href='http://localhost:8082' target='_blank'>Redis Commander (порт 8082)</a></li>
  <li><a href='http://localhost:9200' target='_blank'>Elasticsearch (порт 9200)</a></li>
  <li><a href='http://localhost:8123/?query=SELECT+1' target='_blank'>ClickHouse (порт 8123)</a></li>
</ul>";
?>
