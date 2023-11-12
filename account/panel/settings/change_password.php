<?php
require_once '../../plugins/auth_check.php';
session_start();

// Получение email пользователя из сессии
$email = $_SESSION['email'];

// Инициализация массива для JSON-ответа
$response = array();

// Проверка наличия данных формы
if (isset($_POST['oldPasswordInput'], $_POST['newPasswordInput'], $_POST['confirmPasswordInput'])) {
    $oldPassword = $_POST['oldPasswordInput'];
    $newPassword = $_POST['newPasswordInput'];
    $confirmPassword = $_POST['confirmPasswordInput'];

    // Валидация паролей
    if ($newPassword !== $confirmPassword) {
        $response["error"] = "Пароли не совпадают.";
    } else {
        // Получение хэша пароля из базы данных для текущего пользователя
        $sql = "SELECT password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentUserPasswordHash = $row['password'];
        } else {
            $response["error"] = "Пользователь не найден.";
        }

        if (password_verify($oldPassword, $currentUserPasswordHash)) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Обновление пароля в базе данных
            $sql = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $newPasswordHash, $email);
            $result = $stmt->execute();
            if ($result) {
                $currentTimeTime = date("H:i");

                // Отправка нового пароля на email
                $subject = "Ваш новый пароль";
                $message = "Здравствуйте! Сегодня в $currentTimeTime ваш пароль был изменён на: " . $newPassword . "\nЕсли это не вы, то срочно обратитесь в службу поддержки";
                $headers = "From: no-reply@siteinternet.ru\r\n" .
                    "MIME-Version: 1.0" . "\r\n" .
                    "Content-type: text/plain; charset=UTF-8" . "\r\n";

                if (mail($email, $subject, $message, $headers)) {
                    $response["success"] = "Пароль успешно изменен. Новый пароль отправлен на ваш email.";
                } else {
                    $response["error"] = "Ошибка при отправке нового пароля на email.";
                }
            } else {
                $response["error"] = "Ошибка обновления пароля: " . $conn->error;
            }
        } else {
            $response["error"] = "Неверный старый пароль.";
        }
    }
} else {
    $response["error"] = "Необходимо заполнить все поля.";
}

$conn->close();

// Возвращаем JSON-ответ
echo json_encode($response);
?>
