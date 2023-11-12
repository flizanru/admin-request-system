<?php

$host = "localhost"; // адрес сервера базы данных
$username = "cybernuts_admin"; // имя пользователя базы данных
$password = ""; // пароль пользователя базы данных
$database = "cybernuts_admin"; // имя базы данных

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

?>
