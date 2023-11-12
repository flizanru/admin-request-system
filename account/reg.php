<?php

require_once "../config.php";

// Генерация уникального айди
$uniqueId = generateUniqueId($conn);

// Функция для генерации уникального айди
function generateUniqueId($conn) {
    $uniqueId = mt_rand(10000000, 99999999); // Генерация случайного числа из 8 цифр

    // Проверка уникальности айди
    $checkIdQuery = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($checkIdQuery);
    $stmt->bind_param("s", $uniqueId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Если айди уже существует, сгенерировать новый
        $uniqueId = generateUniqueId($conn);
    }

    $stmt->close();

    return $uniqueId;
}

// Получение данных из формы регистрации
$login = $_POST['login'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];
$gRecaptchaResponse = $_POST['g-recaptcha-response'];

// Проверка наличия подстроки "@gmail.com"
// if (strpos($email, '@gmail.com') !== false) {
//     $error = "Регистрация по почте Gmail временно недоступна";
//     header("Location: register.php?error=" . urlencode($error) . "&login=" . urlencode($login) . "&email=" . urlencode($email) . "&password=" . urlencode($password) . "&confirm_password=" . urlencode($confirmPassword));
//     exit();
// }

// Проверка reCAPTCHA
$secretKey = "";
$recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify";
$recaptchaResponse = file_get_contents($recaptchaUrl . "?secret=" . $secretKey . "&response=" . $gRecaptchaResponse);
$recaptchaResult = json_decode($recaptchaResponse);
if (!$recaptchaResult->success) {
    $error = "Ошибка при проверке reCAPTCHA";
    header("Location: register.php?error=" . urlencode($error) . "&login=" . urlencode($login) . "&email=" . urlencode($email) . "&password=" . urlencode($password) . "&confirm_password=" . urlencode($confirmPassword));
    exit();
}

// Получение IP-адреса пользователя
$userIp = $_SERVER['REMOTE_ADDR'];

// Выборка данных о попытках регистрации для данного IP-адреса
$attemptsQuery = "SELECT * FROM registration_attempts WHERE ip_address = ?";
$stmt = $conn->prepare($attemptsQuery);
$stmt->bind_param("s", $userIp);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$currentTime = new DateTime();
$maxAttempts = 10;
$blockTimeInMinutes = 30;

if ($row) {
    $lastAttempt = new DateTime($row['last_attempt']);
    $interval = $currentTime->diff($lastAttempt);
    $minutesSinceLastAttempt = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

    if ($row['attempt_count'] >= $maxAttempts && $minutesSinceLastAttempt < $blockTimeInMinutes) {
        $error = "Регистрация временно недоступна";
        header("Location: register.php?error=" . urlencode($error));
        exit();
    } elseif ($minutesSinceLastAttempt >= $blockTimeInMinutes) {
        // Сброс счетчика попыток, если прошло достаточно времени с последней попытки
        $updateAttemptsQuery = "UPDATE registration_attempts SET attempt_count = 1, last_attempt = ? WHERE ip_address = ?";
        $stmt = $conn->prepare($updateAttemptsQuery);
        $currentDateTime = $currentTime->format("Y-m-d H:i:s");
        $stmt->bind_param("ss", $currentDateTime, $userIp);
        $stmt->execute();
    } else {
        // Увеличение счетчика попыток
        $updateAttemptsQuery = "UPDATE registration_attempts SET attempt_count = attempt_count + 1, last_attempt = ? WHERE ip_address = ?";
        $stmt = $conn->prepare($updateAttemptsQuery);
        $currentDateTime = $currentTime->format("Y-m-d H:i:s");
        $stmt->bind_param("ss", $currentDateTime, $userIp);
        $stmt->execute();
    }
} else {
    // Вставка новой записи с данными о попытке регистрации
    $insertAttemptsQuery = "INSERT INTO registration_attempts (ip_address, attempt_count, last_attempt) VALUES (?, 1, ?)";
    $stmt = $conn->prepare($insertAttemptsQuery);
    $currentDateTime = $currentTime->format("Y-m-d H:i:s");
    $stmt->bind_param("ss", $userIp, $currentDateTime);
    $stmt->execute();
}

// Проверка уникальности имени пользователя
$checkUsernameQuery = "SELECT * FROM users WHERE login = ?";
$stmt = $conn->prepare($checkUsernameQuery);
$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $error = "Имя пользователя уже занято";
    header("Location: register.php?error=" . urlencode($error) . "&login=" . urlencode($login) . "&email=" . urlencode($email) . "&password=" . urlencode($password) . "&confirm_password=" . urlencode($confirmPassword));
    exit();
}
$stmt->close();

// Проверка уникальности email
$checkEmailQuery = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($checkEmailQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $error = "Email уже зарегистрирован";
    header("Location: register.php?error=" . urlencode($error) . "&login=" . urlencode($login) . "&email=" . urlencode($email) . "&password=" . urlencode($password) . "&confirm_password=" . urlencode($confirmPassword));
    exit();
}
$stmt->close();

// Проверка совпадения паролей
if ($password !== $confirmPassword) {
    $error = "Пароли не совпадают";
    header("Location: register.php?error=" . urlencode($error) . "&login=" . urlencode($login) . "&email=" . urlencode($email) . "&password=" . urlencode($password) . "&confirm_password=" . urlencode($confirmPassword));
    exit();
}

// Проверка капчи
session_start();
    // Генерация случайного кода активации
    $activationCode = md5(uniqid(rand(), true));

    // Хеширование пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Получение текущей даты и времени
$registrationDate = date("Y-m-d H:i:s");

// Подготовка и выполнение SQL-запроса для вставки данных в таблицу
$insertQuery = "INSERT INTO users (id, login, email, password, activation_code, registration_date) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("ssssss", $uniqueId, $login, $email, $hashedPassword, $activationCode, $registrationDate);
    if ($stmt->execute()) {
        // Отправка письма с подтверждением почты
$subject = "Регистрация на CyberNutsRP";
$message = "Приветствуем тебя, дорогой пользователь!\n\nМы рады, что ты захотел зарегистрироваться на нашем сайте, но данный сервис используется только администрацией CyberNutsRP. Если вы являетесь администратором CyberNutsRP, то обратитесь к техническому администратору сайта, чтобы он принял вашу заявку на регистрацию на сайте.\n\n";
$message .= "Если вы ошиблись и не являетесь администратором CyberNutsRP, то пожалуйста, проигнорируйте это сообщение.\n\n";
$headers = "From: no-reply@siteinternet.ru";

        // Отправка письма
        mail($email, $subject, $message, $headers);

        die("На вашу почту была отправлена инструкция");
    } else {
        $error = "Ошибка при регистрации: " . $conn->error;
        header("Location: register.php?error=" . urlencode($error) . "&login=" . urlencode($login) . "&email=" . urlencode($email) . "&password=" . urlencode($password) . "&confirm_password=" . urlencode($confirmPassword));
        exit();
    }

$conn->close();
?>
