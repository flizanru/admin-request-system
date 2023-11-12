<?php
// Инициируем сессию
session_start();

// Очищаем все переменные сессии
$_SESSION = array();

// Удаляем сессионные cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу входа или любую другую страницу по вашему выбору
header("Location: log.php");
exit();
?>
