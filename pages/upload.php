<?php
// Подключение к базе данных
$dsn = 'mysql:host=localhost;dbname=site_db;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Обработка загрузки файла через AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $uploadDir = '../images/';
    $filePath = $uploadDir . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $fileName = $file['name'];
        $fileSize = $file['size'];

        // Запись информации о файле в базу данных
        $stmt = $pdo->prepare("INSERT INTO Pictures (name, size, imagepath) VALUES (:name, :size, :imagepath)");
        $stmt->execute([
            ':name' => $fileName,
            ':size' => $fileSize,
            ':imagepath' => $filePath
        ]);

        // Возвращаем успешный ответ для AJAX-запроса
        echo json_encode(["status" => "success", "message" => "Файл успешно загружен и информация добавлена в базу данных."]);
        exit;
    } else {
        // Возвращаем ошибку для AJAX-запроса
        echo json_encode(["status" => "error", "message" => "Ошибка загрузки файла."]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузить изображение</title>
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


        .upload-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.3);
            max-width: 500px;
        }


        .upload-container h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }


        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .upload-btn {
            font-size: 18px;
            font-weight: bold;
            padding: 12px 24px;
            color: #ffffff;
            background-color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .upload-btn:hover {
            transform: scale(1.1);
            background-color: #444;
        }


        .upload-btn-wrapper input[type="file"] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
        }

        /* Прогресс-бар */
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #ddd;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }

        .progress-fill {
            height: 100%;
            width: 0;
            background-color: #4CAF50;
            border-radius: 10px;
            transition: width 0.2s ease;
        }


        .submit-btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .submit-btn:hover {
            transform: scale(1.05);
            background-color: #45a049;
        }


        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: #ffffff;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
<div class="upload-container">
    <h1>Загрузить файл</h1>
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="upload-btn-wrapper">
            <button class="upload-btn">Выбрать файл &#128194;</button>
            <input type="file" name="image" required>
        </div>

        <!-- Прогресс-бар -->
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        <button type="submit" class="submit-btn">Загрузить</button>
    </form>
</div>


<div id="toast" class="toast"></div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'upload.php', true);


        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                document.getElementById('progressFill').style.width = percentComplete + '%';
            }
        };


        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    showToast(response.message);
                    document.getElementById('uploadForm').reset();
                    document.getElementById('progressFill').style.width = '0%';


                    setTimeout(() => {
                        window.location.href = '../index.php';
                    }, 3000);
                } else {
                    showToast(response.message, true);
                    document.getElementById('progressFill').style.width = '0%';
                }
            } else {
                showToast("Ошибка загрузки файла", true);
                document.getElementById('progressFill').style.width = '0%';
            }
        };

        xhr.onerror = function() {
            showToast("Ошибка сети. Попробуйте снова.", true);
            document.getElementById('progressFill').style.width = '0%';
        };

        xhr.send(formData);
    });


    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.innerText = message;
        toast.style.backgroundColor = isError ? '#f44336' : '#4CAF50';
        toast.classList.add('show');


        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
</body>
</html>
