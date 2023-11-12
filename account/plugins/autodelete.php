<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/config.php";

// Запрос на выборку пользователей с is_active = 0 и которые зарегистрированы более 1 дня назад
$query = "SELECT * FROM users WHERE is_active = 0 AND registration_date < DATE_SUB(NOW(), INTERVAL 1 DAY)";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // Перебор всех строк результата
    while($row = mysqli_fetch_assoc($result)) {
        $user_email = $row['email'];
        $user_id = $row['id'];

        // Отправка письма пользователю
        $subject = 'Ваш аккаунт удален из-за неактивности';
        $message = 'Ваш аккаунт был удален из-за неактивности. Если вы хотите создать новый аккаунт, пожалуйста, зарегистрируйтесь снова.';
        $headers = 'From: no-reply@siteinternet.ru' . "\r\n" .
            'Reply-To: no-reply@siteinternet.ru' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($user_email, $subject, $message, $headers);

        // Удаление аккаунта пользователя из базы данных
        $delete_query = "DELETE FROM users WHERE id = $user_id";
        mysqli_query($conn, $delete_query);
    }
}

// Закрытие соединения с базой данных
mysqli_close($conn);
?>