<<<<<<< HEAD
﻿<?php
// Подключаем автозагрузчик Composer для ЛР6
require_once 'vendor/autoload.php';

=======
<?php
require 'vendor/autoload.php';
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
use App\Services\RedisService;
use App\Services\ElasticsearchService;
use App\Services\ClickHouseService;

<<<<<<< HEAD
// Инициализация сервисов
$redisService = new RedisService();
$elasticService = new ElasticsearchService();
$clickhouseService = new ClickHouseService();

// Обработка форм
$results = [
    'redis' => [],
    'elastic' => [],
    'clickhouse' => []
];

if ($_POST) {
    if (isset($_POST['redis_action'])) {
        try {
            switch ($_POST['redis_action']) {
                case 'cache_student':
                    $student = [
                        'id' => uniqid(),
                        'full_name' => 'Тестовый Студент',
                        'faculty' => 'Компьютерные науки',
                        'course' => '2 курс',
                        'group_name' => 'КН-21-01',
                        'timestamp' => time()
                    ];
                    $success = $redisService->cacheStudent($student);
                    $results['redis']['cache'] = $success ? '✅ Студент закеширован' : '❌ Ошибка кеширования';
                    break;
                case 'get_stats':
                    $stats = [
                        'total_students' => 150,
                        'today_registrations' => 12,
                        'popular_faculty' => 'Компьютерные науки'
                    ];
                    $redisService->cacheStats($stats);
                    $cachedStats = $redisService->getCachedStats();
                    $results['redis']['stats'] = $cachedStats ? '✅ Статистика получена: ' . json_encode($cachedStats, JSON_UNESCAPED_UNICODE) : '❌ Статистика не найдена';
                    break;
                case 'increment_counter':
                    $count = $redisService->incrementCounter();
                    $results['redis']['counter'] = "✅ Счётчик увеличен: $count";
                    break;
            }
        } catch (Exception $e) {
            $results['redis']['error'] = "❌ Ошибка Redis: " . $e->getMessage();
        }
    }

    if (isset($_POST['elastic_action'])) {
        try {
            $elasticService->ensureIndexExists();
            switch ($_POST['elastic_action']) {
                case 'index_document':
                    $student = [
                        'full_name' => 'Анна Петрова',
                        'email' => 'anna@example.com',
                        'phone' => '+79991234567',
                        'faculty' => 'Экономика',
                        'course' => '1 курс',
                        'group_name' => 'ЭК-22-03',
                        'birth_date' => '2005-03-15',
                        'address' => 'г. Москва, ул. Примерная, д. 10',
                        'registration_date' => date('c')
                    ];
                    $success = $elasticService->indexStudent($student);
                    $results['elastic']['index'] = $success ? '✅ Документ проиндексирован' : '❌ Ошибка индексации';
                    break;
                case 'search':
                    $query = $_POST['search_query'] ?? '';
                    $filters = [];
                    if (!empty($_POST['faculty_filter'])) {
                        $filters['faculty'] = $_POST['faculty_filter'];
                    }
                    $hits = $elasticService->searchStudents($query, $filters);
                    $results['elastic']['search'] = [
                        'count' => count($hits),
                        'hits' => $hits
                    ];
                    break;
                case 'get_stats':
                    $stats = $elasticService->getStats();
                    $results['elastic']['stats'] = $stats;
                    break;
            }
        } catch (Exception $e) {
            $results['elastic']['error'] = "❌ Ошибка Elasticsearch: " . $e->getMessage();
        }
    }

    if (isset($_POST['clickhouse_action'])) {
        try {
            switch ($_POST['clickhouse_action']) {
                case 'insert_analytics':
                    $analytics = [
                        'full_name' => 'Иван Сидоров',
                        'email' => 'ivan@example.com',
                        'faculty' => 'Менеджмент',
                        'course' => '3 курс',
                        'group_name' => 'МН-20-02',
                        'duration_seconds' => 45,
                        'status' => 'completed'
                    ];
                    $success = $clickhouseService->insertRegistrationAnalytics($analytics);
                    $results['clickhouse']['insert'] = $success ? '✅ Данные аналитики добавлены' : '❌ Ошибка добавления';
                    break;
                case 'daily_stats':
                    $dailyStats = $clickhouseService->getDailyRegistrations();
                    $results['clickhouse']['daily'] = $dailyStats;
                    break;
                case 'faculty_performance':
                    $performance = $clickhouseService->getFacultyPerformance();
                    $results['clickhouse']['performance'] = $performance;
                    break;
                case 'course_trends':
                    $trends = $clickhouseService->getCourseTrends();
                    $results['clickhouse']['trends'] = $trends;
                    break;
            }
        } catch (Exception $e) {
            $results['clickhouse']['error'] = "❌ Ошибка ClickHouse: " . $e->getMessage();
        }
=======
$redis = new RedisService();
$elastic = new ElasticsearchService();
$clickhouse = new ClickHouseService();

if ($_POST) {
    $student = ['id' => uniqid(), 'full_name' => 'Иван Иванов', 'faculty' => 'Информационные технологии', 'course' => '2 курс'];
    if ($_POST['action'] === 'redis_cache') {
        $redis->cacheStudent($student);
        $result = '✅ Студент закеширован в Redis';
    } elseif ($_POST['action'] === 'redis_counter') {
        $count = $redis->incrementCounter();
        $result = "✅ Счётчик студентов: $count";
    } elseif ($_POST['action'] === 'elastic_index') {
        $elastic->ensureIndex();
        $elastic->indexStudent($student);
        $result = '✅ Студент проиндексирован в Elasticsearch';
    } elseif ($_POST['action'] === 'elastic_search') {
        $q = $_POST['query'] ?? '';
        $f = $_POST['faculty'] ?? '';
        $hits = $elastic->search($q, $f);
        $result = "✅ Найдено: " . count($hits) . " студента(ов)";
    } elseif ($_POST['action'] === 'clickhouse_insert') {
        $clickhouse->insert($student);
        $result = '✅ Данные добавлены в ClickHouse';
    } elseif ($_POST['action'] === 'clickhouse_stats') {
        $stats = $clickhouse->getFacultyStats();
        $result = "✅ Статистика: " . count($stats) . " факультет(ов)";
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР6 - NoSQL базы данных для системы регистрации студентов</title>
    <style>
        :root {
            --redis: #dc382d;
            --elastic: #00c5b7;
            --clickhouse: #ffcc02;
            --dark: #2c3e50;
            --light: #ecf0f1;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        h1 {
            color: var(--dark);
            margin-bottom: 10px;
        }
        .subtitle {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .nav-btn {
            padding: 12px 25px;
            background: var(--dark);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 2px solid var(--dark);
            font-weight: 600;
        }
        .nav-btn:hover {
            background: white;
            color: var(--dark);
            transform: translateY(-2px);
        }
        .databases-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        .database-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .database-card.redis {
            border-top: 5px solid var(--redis);
        }
        .database-card.elastic {
            border-top: 5px solid var(--elastic);
        }
        .database-card.clickhouse {
            border-top: 5px solid var(--clickhouse);
        }
        .database-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .database-icon {
            font-size: 2em;
            margin-right: 15px;
        }
        .database-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .action-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .action-btn.redis {
            background: var(--redis);
            color: white;
        }
        .action-btn.elastic {
            background: var(--elastic);
            color: white;
        }
        .action-btn.clickhouse {
            background: var(--clickhouse);
            color: var(--dark);
        }
        .action-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        .results {
            background: var(--light);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .result-item {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            border-left: 4px solid var(--dark);
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        .search-input {
            flex: 1;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        pre {
            background: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .databases-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Лабораторная работа 6</h1>
            <div class="subtitle">NoSQL базы данных - Redis, Elasticsearch, ClickHouse для системы регистрации студентов</div>
            <div class="nav-buttons">
                <a href="/index.html" class="nav-btn">🏠 На главную</a>
                <a href="/lab5/index.php" class="nav-btn">💾 К ЛР5</a>
                <a href="http://localhost:8082" target="_blank" class="nav-btn">🔴 Redis Commander</a>
                <a href="http://localhost:5601" target="_blank" class="nav-btn">🔍 Kibana</a>
            </div>
        </div>
        <div class="databases-grid">
            <!-- Redis Card -->
            <div class="database-card redis">
                <div class="database-header">
                    <div class="database-icon">🔴</div>
                    <div>
                        <h2>Redis</h2>
                        <p>Кеширование и быстрый доступ к данным студентов</p>
                    </div>
                </div>
                <form method="POST" class="database-actions">
                    <button type="submit" name="redis_action" value="cache_student" class="action-btn redis">
                        💾 Кешировать студента
                    </button>
                    <button type="submit" name="redis_action" value="get_stats" class="action-btn redis">
                        📊 Получить статистику
                    </button>
                    <button type="submit" name="redis_action" value="increment_counter" class="action-btn redis">
                        🔢 Увеличить счётчик
                    </button>
                </form>
                <?php if (!empty($results['redis'])): ?>
                <div class="results">
                    <h4>Результаты Redis:</h4>
                    <?php foreach ($results['redis'] as $key => $result): ?>
                        <div class="result-item">
                            <strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong> 
                            <?php if (is_array($result)): ?>
                                <pre><?= json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                            <?php else: ?>
                                <?= $result ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- Elasticsearch Card -->
            <div class="database-card elastic">
                <div class="database-header">
                    <div class="database-icon">🔍</div>
                    <div>
                        <h2>Elasticsearch</h2>
                        <p>Поиск и анализ регистраций студентов</p>
                    </div>
                </div>
                <form method="POST" class="database-actions">
                    <button type="submit" name="elastic_action" value="index_document" class="action-btn elastic">
                        📝 Индексировать регистрацию
                    </button>
                    <div class="search-form">
                        <input type="text" name="search_query" placeholder="Поиск студентов..." class="search-input">
                        <select name="faculty_filter" class="search-input">
                            <option value="">Все факультеты</option>
                            <option value="Компьютерные науки">Компьютерные науки</option>
                            <option value="Экономика">Экономика</option>
                            <option value="Менеджмент">Менеджмент</option>
                        </select>
                        <button type="submit" name="elastic_action" value="search" class="action-btn elastic">
                            🔎 Поиск
                        </button>
                    </div>
                    <button type="submit" name="elastic_action" value="get_stats" class="action-btn elastic">
                        📈 Статистика по факультетам
                    </button>
                </form>
                <?php if (!empty($results['elastic'])): ?>
                <div class="results">
                    <h4>Результаты Elasticsearch:</h4>
                    <?php foreach ($results['elastic'] as $key => $result): ?>
                        <div class="result-item">
                            <strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong>
                            <?php if ($key === 'search' && is_array($result)): ?>
                                <div>Найдено записей: <?= $result['count'] ?></div>
                                <?php if (!empty($result['hits'])): ?>
                                    <div class="stats-grid">
                                        <?php foreach (array_slice($result['hits'], 0, 3) as $hit): ?>
                                            <div class="stat-item">
                                                <div class="stat-value">🎓</div>
                                                <div><?= htmlspecialchars($hit['_source']['full_name'] ?? 'N/A') ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (is_array($result)): ?>
                                <pre><?= json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                            <?php else: ?>
                                <?= $result ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- ClickHouse Card -->
            <div class="database-card clickhouse">
                <div class="database-header">
                    <div class="database-icon">⚡</div>
                    <div>
                        <h2>ClickHouse</h2>
                        <p>Аналитика регистраций в реальном времени</p>
                    </div>
                </div>
                <form method="POST" class="database-actions">
                    <button type="submit" name="clickhouse_action" value="insert_analytics" class="action-btn clickhouse">
                        📊 Добавить аналитику
                    </button>
                    <button type="submit" name="clickhouse_action" value="daily_stats" class="action-btn clickhouse">
                        📅 Ежедневная статистика
                    </button>
                    <button type="submit" name="clickhouse_action" value="faculty_performance" class="action-btn clickhouse">
                        🎓 Эффективность факультетов
                    </button>
                    <button type="submit" name="clickhouse_action" value="course_trends" class="action-btn clickhouse">
                        📈 Тренды по курсам
                    </button>
                </form>
                <?php if (!empty($results['clickhouse'])): ?>
                <div class="results">
                    <h4>Результаты ClickHouse:</h4>
                    <?php foreach ($results['clickhouse'] as $key => $result): ?>
                        <div class="result-item">
                            <strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong>
                            <?php if (is_array($result) && !empty($result)): ?>
                                <div style="overflow-x: auto;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background: #34495e; color: white;">
                                                <?php foreach ($result[0] as $colIndex => $column): ?>
                                                    <th style="padding: 8px; border: 1px solid #ddd;">Колонка <?= $colIndex + 1 ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($result as $row): ?>
                                                <tr>
                                                    <?php foreach ($row as $cell): ?>
                                                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($cell) ?></td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php elseif (is_array($result)): ?>
                                <div>Нет данных</div>
                            <?php else: ?>
                                <?= $result ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Информация о базах данных -->
        <div class="database-card" style="background: white; padding: 25px; border-radius: 15px; margin-top: 20px;">
            <h2>ℹ️ Информация о NoSQL базах данных</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">🔴</div>
                    <div><strong>Redis</strong><br>Порт: 6379<br>Коммандер: 8082</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">🔍</div>
                    <div><strong>Elasticsearch</strong><br>Порт: 9200<br>Kibana: 5601</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">⚡</div>
                    <div><strong>ClickHouse</strong><br>Порт: 8123<br>Для аналитики</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">💾</div>
                    <div><strong>MySQL</strong><br>Порт: 3307<br>Adminer: 8081</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
=======
    <title>ЛР6 — Регистрация студента (NoSQL)</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fa; padding: 20px; }
        .card { background: white; margin: 15px 0; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px #ccc; }
        .btn { padding: 10px 15px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; color: white; }
        .redis { background: #e74c3c; }
        .elastic { background: #2ecc71; }
        .clickhouse { background: #f39c12; color: #000; }
        .result { margin-top: 10px; padding: 10px; background: #e8f4ff; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🎓 ЛР6 — Вариант 1: Регистрация студента</h1>
    <p>Работа с NoSQL: Redis, Elasticsearch, ClickHouse</p>

    <div class="card">
        <h2>🔴 Redis — Кеширование</h2>
        <form method="POST">
            <button type="submit" name="action" value="redis_cache" class="btn redis">Кешировать студента</button>
            <button type="submit" name="action" value="redis_counter" class="btn redis">Счётчик</button>
        </form>
    </div>

    <div class="card">
        <h2>🔍 Elasticsearch — Поиск</h2>
        <form method="POST">
            <input name="query" placeholder="ФИО" style="margin:5px;padding:5px">
            <select name="faculty" style="margin:5px">
                <option value="">Все</option>
                <option value="Информационные технологии">ИТ</option>
            </select>
            <button type="submit" name="action" value="elastic_search" class="btn elastic">Поиск</button>
            <button type="submit" name="action" value="elastic_index" class="btn elastic">Добавить</button>
        </form>
    </div>

    <div class="card">
        <h2>⚡ ClickHouse — Аналитика</h2>
        <form method="POST">
            <button type="submit" name="action" value="clickhouse_insert" class="btn clickhouse">Добавить</button>
            <button type="submit" name="action" value="clickhouse_stats" class="btn clickhouse">Статистика</button>
        </form>
    </div>

    <?php if (isset($result)): ?>
        <div class="result"><?= htmlspecialchars($result) ?></div>
    <?php endif; ?>

    <p><a href="/index.html">🏠 На главную</a></p>
</body>
</html>
>>>>>>> 52e39ffe47697736954fb2c2fce9efe5f9af046d
