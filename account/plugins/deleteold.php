<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/config.php";

$date_threshold = new DateTime();
$date_threshold->modify('-7 days');
$date_threshold_formatted = $date_threshold->format('Y-m-d H:i:s');

// Используем подготовленный запрос для выборки записей с application_date старше 7 дней
$stmt = $conn->prepare("SELECT * FROM applications WHERE application_date <= ?");
$stmt->bind_param("s", $date_threshold_formatted);
$stmt->execute();
$result = $stmt->get_result();

// Удаляем найденные записи из таблиц applications и activation_codes
while ($row = $result->fetch_assoc()) {
    $code_to_delete = $row['code'];

    // Удаляем запись из таблицы applications
    $stmt_delete_app = $conn->prepare("DELETE FROM applications WHERE code = ?");
    $stmt_delete_app->bind_param("s", $code_to_delete);
    $stmt_delete_app->execute();

    // Удаляем запись из таблицы activation_codes
    $stmt_delete_code = $conn->prepare("DELETE FROM activation_codes WHERE code = ?");
    $stmt_delete_code->bind_param("s", $code_to_delete);
    $stmt_delete_code->execute();
}

// Закрываем подготовленные запросы и освобождаем результаты запроса
$stmt->close();
$result->free();

// Закрываем соединение с базой данных
$conn->close();
?>