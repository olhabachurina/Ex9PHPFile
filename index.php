<?php

$dsn = 'mysql:host=localhost;dbname=site_db;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получение количества изображений в таблице Pictures
$query = $pdo->query("SELECT COUNT(*) AS count FROM Pictures");
$count = $query->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <style>

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1a1a2e;
            color: #ffffff;
        }


        .main-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.3);
            max-width: 500px;
        }


        .main-container h1 {
            font-size: 36px;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-out;
        }


        .main-container p {
            font-size: 18px;
            margin-bottom: 20px;
            color: #d3d3d3;
            animation: fadeIn 1.5s ease-out;
        }


        .main-container a {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .main-container a:hover {
            transform: scale(1.1);
            background-color: #4CAF50;
        }


        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
    <h1>Добро пожаловать на наш сайт!</h1>
    <p>Текущее количество изображений: <?= $count; ?></p>
    <a href="pages/upload.php">Загрузить изображение</a>
    <a href="pages/show.php">Просмотреть изображения</a>
</div>
</body>
</html>
