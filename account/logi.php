<?php
require_once '../vendor/autoload.php';
require_once "shluxa.php";

ob_start();

function getDeviceName() {
    $detect = new Mobile_Detect;
    $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Phone') : 'Computer');
    $scriptVersion = $detect->getScriptVersion();
    return "{$deviceType} ({$scriptVersion})";
}

function getCityFromIpAddress($ip_address, $api_key) {
    $url = "https://geocode-maps.yandex.ru/1.x/?apikey={$api_key}&geocode={$ip_address}&format=json";
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
        ],
    ]);
    $json = file_get_contents($url, false, $context);
    $data = json_decode($json);

    if (isset($data->response->GeoObjectCollection->featureMember[0]->GeoObject)) {
        $geoObject = $data->response->GeoObjectCollection->featureMember[0]->GeoObject;
        if (isset($geoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName)) {
            return $geoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
        }
    }

    return 'Unknown';
}

function generate_session_token() {
    return bin2hex(random_bytes(32));
}

function sendLoginNotification($email, $ip_address) {
    $subject = 'Оповещение безопасности';
    $message = "Прямо сейчас вы вошли в аккаунт на сайте cybernuts.ru с IP адреса $ip_address";
    $from = 'no-reply@siteinternet.ru';
    sendMail($email, $subject, $message, $from);
}

$recaptcha_secret_key = ''; 

function verifyRecaptcha($recaptcha_response, $secret_key) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response,
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result_json = json_decode($result, true);

    return $result_json['success'];
}

$recaptcha_response = $_POST['g-recaptcha-response'];

if (!verifyRecaptcha($recaptcha_response, $recaptcha_secret_key)) {
    $error = "Вы не прошли проверку reCAPTCHA";
    header("Location: log.php?error=" . urlencode($error));
    exit();
}

require_once "../config.php";

$email = $_POST['email'];

$sql = "SELECT id, email FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id1 = $row['id'];
    } else {
        echo "Запись с таким email не найдена";
    }

    $stmt->close();
} else {
    echo "Ошибка при создании подготовленного выражения";
}

$currentDateDay = date("Y-m-d");
$currentTimeTime = date("H:i");

function sendBanNotification($email, $ip_address, $currentDateDay, $currentTimeTime, $id1) {
    global $conn;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Некорректный адрес электронной почты";
        return;
    }

    $sql_last_notification = "SELECT time FROM notifications WHERE name = ? ORDER BY time DESC LIMIT 1";
    $stmt_last_notification = $conn->prepare($sql_last_notification);
    $baninfo = "baninfo";
    $stmt_last_notification->bind_param("s", $baninfo);
    $stmt_last_notification->execute();
    $result_last_notification = $stmt_last_notification->get_result();
    $row_last_notification = $result_last_notification->fetch_assoc();
    $stmt_last_notification->close();

    if ($row_last_notification) {
        $last_notification_time = strtotime($row_last_notification['time']);
        $time_difference = time() - $last_notification_time;

        if ($time_difference >= 600) {
            $subject = 'Уведомление о блокировке';
            $message = "Сегодня в $currentTimeTime на ваш акаунт было произведено несколько попыток войти в ваш аккаунт\nСвязи с этим мы временно заблокировали доступ к вашему аккаунту.\nЕсли это были не вы, то мы рекомендуем обратиться вам в поддержку немедленно.";
            $from = 'no-reply@siteinternet.ru';
            sendMail($email, $subject, $message, $from);

            $sql_insert = "INSERT INTO notifications (name, time) VALUES (?, NOW())";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("s", $baninfo);
            $stmt_insert->execute();
            $stmt_insert->close();

            // Удаление всех сессий пользователя с идентификатором $id1
            $sql_delete_sessions = "DELETE FROM sessions WHERE user_id = ?";
            $stmt_delete_sessions = $conn->prepare($sql_delete_sessions);
            $stmt_delete_sessions->bind_param("i", $id1);
            $stmt_delete_sessions->execute();
            $stmt_delete_sessions->close();
        }
    } else {
        $subject = 'Уведомление о блокировке';
        $message = "Сегодня в $currentTimeTime на ваш акаунт было произведено несколько попыток войти в ваш аккаунт\nСвязи с этим мы временно заблокировали доступ к вашему аккаунту.\nЕсли это были не вы, то мы рекомендуем обратиться вам в поддержку немедленно.";
        $from = 'no-reply@siteinternet.ru';
        sendMail($email, $subject, $message, $from);

        $sql_insert = "INSERT INTO notifications (name, time) VALUES (?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("s", $baninfo);
        $stmt_insert->execute();
        $stmt_insert->close();

        $sql_delete_sessions = "DELETE FROM sessions WHERE user_id = ?";
        $stmt_delete_sessions = $conn->prepare($sql_delete_sessions);
        $stmt_delete_sessions->bind_param("i", $id1);
        $stmt_delete_sessions->execute();
        $stmt_delete_sessions->close();
    }
}

session_start();

$ip_address = $_SERVER['REMOTE_ADDR'];

$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];
    $isActive = $row['is_active'];

    if (password_verify($password, $hashedPassword)) {
        if ($isActive == 1) {
            $session_token = generate_session_token();

            $device_name = getDeviceName();
            $city = 'Unknown';

            $user_id = $row['id'];
            $sql = "INSERT INTO sessions (user_id, session_token, ip_address, device_name, city, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt_session = $conn->prepare($sql);
            $stmt_session->bind_param("issss", $user_id, $session_token, $ip_address, $device_name, $city);
            $stmt_session->execute();
            $stmt_session->close();

            $sql_attempts = "SELECT COUNT(*) AS attempts FROM attempts WHERE user_id = ? AND created_at >= (NOW() - INTERVAL 10 MINUTE)";
            $stmt_attempts = $conn->prepare($sql_attempts);
            $stmt_attempts->bind_param("i", $user_id);
            $stmt_attempts->execute();
            $result_attempts = $stmt_attempts->get_result();
            $row_attempts = $result_attempts->fetch_assoc();
            $attempts = $row_attempts['attempts'];
            $stmt_attempts->close();

            if ($attempts >= 5) {
                $error = "Подозрительная активность аккаунта. Аккаунт заблокирован";
                sendBanNotification($email, $ip_address, $currentDateDay, $currentTimeTime, $id1);
                header("Location: log.php?error=" . urlencode($error));
                exit();
            }

            $sql_insert_attempt = "INSERT INTO attempts (user_id, ip_address, created_at) VALUES (?, ?, NOW())";
            $stmt_insert_attempt = $conn->prepare($sql_insert_attempt);
            $stmt_insert_attempt->bind_param("is", $user_id, $ip_address);
            $stmt_insert_attempt->execute();
            $stmt_insert_attempt->close();

            $_SESSION['session_token'] = $session_token;
            $_SESSION['email'] = $email;
            $_SESSION['is_authenticated'] = true;

            sendLoginNotification($email, $ip_address);

            header("Location: index.html");
            exit();
        } else {
            $error = "Ваша учетная запись не активирована.";
            header("Location: log.php?error=" . urlencode($error));
            exit();
        }
    } else {
        $error = "Ошибка: Неверный пароль. Пожалуйста, попробуйте еще раз.";
        header("Location: log.php?error=" . urlencode($error));
        exit();
    }
} else {
    $error = "Ошибка: Пользователь с указанным адресом электронной почты не найден.";
    header("Location: log.php?error=" . urlencode($error));
    exit();
}

$stmt->close();
$conn->close();
ob_end_flush();
?>