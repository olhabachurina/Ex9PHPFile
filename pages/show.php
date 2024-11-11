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

// Получение списка изображений
$query = $pdo->query("SELECT * FROM Pictures");
$pictures = $query->fetchAll(PDO::FETCH_ASSOC);

// Если изображение выбрано, получаем данные о нем
$selectedImage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    $imageId = (int)$_POST['image_id'];
    $stmt = $pdo->prepare("SELECT * FROM Pictures WHERE id = :id");
    $stmt->execute([':id' => $imageId]);
    $selectedImage = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр изображений</title>
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


        .show-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }


        .show-container h2 {
            font-size: 36px;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-out;
        }


        .image-select-form select {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #444;
            background-color: #333;
            color: #ffffff;
        }

        .image-select-form button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: #ffffff;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .image-select-form button:hover {
            transform: scale(1.05);
            background-color: #45a049;
        }


        .image-info {
            margin-top: 20px;
            animation: fadeIn 1.5s ease-out;
        }

        .image-info img {
            max-width: 100%;
            height: auto;
            border: 2px solid #ffffff;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        .image-info p {
            font-size: 18px;
            color: #d3d3d3;
            margin: 5px 0;
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
<div class="show-container">
    <h2>Просмотреть изображение</h2>
    <form method="post" class="image-select-form">
        <select name="image_id" required>
            <?php foreach ($pictures as $picture): ?>
                <option value="<?= $picture['id']; ?>"><?= htmlspecialchars($picture['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Показать</button>
    </form>

    <?php if ($selectedImage): ?>
        <div class="image-info">
            <h3>Информация об изображении:</h3>
            <img src="../images/<?= htmlspecialchars(basename($selectedImage['imagepath'])); ?>" alt="<?= htmlspecialchars($selectedImage['name']); ?>">
            <p>Имя файла: <?= htmlspecialchars($selectedImage['name']); ?></p>
            <p>Размер файла: <?= $selectedImage['size']; ?> байт</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>