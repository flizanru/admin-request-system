<?php
session_start();

if (!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    header("Location: https://cybernuts.ru/admin/account/log.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/config.php";

$styleQuery = "SELECT style_url FROM styles WHERE id = 1";
$styleResult = $conn->query($styleQuery);

$styleUrl = "styles4.css";

if ($styleResult && $styleResult->num_rows > 0) {
    $styleRow = $styleResult->fetch_assoc();
    $styleUrl = $styleRow['style_url'];
}

$current_session_token = $_SESSION['session_token'] ?? '';

$email = $_SESSION['email'];
$sql = "SELECT COUNT(*) as session_count FROM sessions s JOIN users u ON s.user_id = u.id WHERE u.email = ? AND s.session_token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $current_session_token);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$sql = "SELECT admin FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

$isAdmin = false;
if (isset($userData['admin']) && $userData['admin'] == 1) {
    $isAdmin = true;
}

$getUserInfoQuery = "SELECT id, login FROM users WHERE email = ?";
$stmt = $conn->prepare($getUserInfoQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $login);
if ($stmt->fetch()) {
    $userId = $id;
    $userLogin = $login;
}

$stmt->close();

if ($row['session_count'] == 0) {
    session_destroy();
    header("Location: logout.php");
    exit();
}
?>