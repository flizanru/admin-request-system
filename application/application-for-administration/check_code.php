<?php
header('Content-Type: application/json');

// Настройки подключения к базе данных
$host = "localhost"; // адрес сервера базы данных
$username = "cybernuts_admin"; // имя пользователя базы данных
$password = ""; // пароль пользователя базы данных
$database = "cybernuts_admin"; // имя базы данных

// Подключаемся к базе данных
$mysqli = new mysqli($host, $username, $password, $database);

// Проверяем подключение
if ($mysqli->connect_error) {
    echo json_encode(['valid' => false]);
    exit;
}

// Устанавливаем кодировку
$mysqli->set_charset("utf8");

// Получаем код активации из запроса
$code = $_POST['code'];

// Используем подготовленный запрос для выборки кода активации
$stmt = $mysqli->prepare("SELECT * FROM activation_codes WHERE code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

$valid = false;

if ($result->num_rows > 0) {
    $code_data = $result->fetch_assoc();
    $current_date = new DateTime();
    $end_date = new DateTime($code_data['dateend']);

    if ($end_date >= $current_date) {
        // Проверяем, была ли ранее подана заявка с использованием этого кода активации
        $stmt = $mysqli->prepare("SELECT * FROM applications WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $app_result = $stmt->get_result();

        if ($app_result->num_rows == 0) {
            $valid = true;
        }

        // Освобождаем результат запроса
        $app_result->free();
    }
}

// Возвращаем результат проверки кода активации
echo json_encode(['valid' => $valid]);

// Закрываем подготовленный запрос и освобождаем результаты запроса
$stmt->close();
$result->free();

// Закрываем соединение с базой данных
$mysqli->close();
?>