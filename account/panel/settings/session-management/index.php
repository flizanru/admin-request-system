<?php
require_once '../../../../vendor/autoload.php';
require_once '../../../plugins/auth_check.php';

function getDeviceName() {
    $detect = new Mobile_Detect;
    $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Phone') : 'Computer');
    $scriptVersion = $detect->getScriptVersion();

    return "{$deviceType} ({$scriptVersion})";
}

function getCityFromIpAddress($ip_address) {
    $url = "http://ip-api.com/json/{$ip_address}?fields=status,message,country,city";
    $json = file_get_contents($url);
    $data = json_decode($json);

    if ($data->status == 'success') {
        return $data->city;
    } else {
        return 'Unknown';
    }
}

if (!isset($_SESSION['is_authenticated']) || !$_SESSION['is_authenticated']) {
    header("Location: log.php");
    exit();
}

// Обработка запроса на закрытие сессии
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['close_session'])) {
    $session_token = $_POST['session_token'];
    $sql = "DELETE FROM sessions WHERE session_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_token);
    $stmt->execute();
    $stmt->close();
}

// Получение списка сессий текущего пользователя
$email = $_SESSION['email'];
$sql = "SELECT s.session_token, s.ip_address, s.device_name, s.city, s.created_at, s.updated_at FROM sessions s JOIN users u ON s.user_id = u.id WHERE u.email = ? GROUP BY s.session_token";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['close_session'])) {
    $session_token = $_POST['session_token'];
    $sql = "DELETE FROM sessions WHERE session_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_token);
    $stmt->execute();
    $stmt->close();

    // Удаление сессии у пользователя
    setcookie("session_token", "", time() - 3600);
}

// Обработка запроса на удаление всех сессий
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['close_all_sessions'])) {
    $sql = "DELETE FROM sessions WHERE user_id = (SELECT id FROM users WHERE email = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
            color: #f8f9fa;
            font-family: 'Montserrat', sans-serif;
        }
        .footer-text {
            color: #dc3545;
        }
        .custom-card {
            background-color: #343a40;
            color: #f8f9fa;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .card-title {
            color: #f8f9fa;
        }
        .card-text {
            color: #f8f9fa;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #545b62;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #bd2130;
            border-color: #bd2130;
        }
        .current-session {
            background-color: #ffc107;
            color: #212529;
            border-radius: 5px;
            padding: 5px 10px;
            margin-left: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between align-items-center py-4">
            <h1 class="text-2xl">Управление сессиями</h1>
            <div class="d-flex align-items-center">
                <span class="current-session">Текущая сессия: <?php echo htmlspecialchars($_SESSION['session_token']); ?></span>
                <a href="../../settings" class="btn btn-secondary">Обратно</a>
            </div>
        </header>

        <main class="mt-8">
            <?php foreach ($sessions as $session): ?>
                <div class="card custom-card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img src="monitor.png" alt="Device Icon" width="32" height="32" />
                            </div>
                            <div>
                                <h5 class="card-title mb-0">
                                    <?php echo htmlspecialchars($session['device_name']); ?> | <?php echo htmlspecialchars($session['ip_address']); ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($session['created_at']); ?> | <?php echo htmlspecialchars($session['updated_at']); ?>
                                </p>
                            </div>
                        </div>
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="session_token" value="<?php echo htmlspecialchars($session['session_token']);?>">
                            <button type="submit" name="close_session" class="btn btn-danger">Закрыть сессию</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
        
        <footer class="mt-4">
            <p class="footer-text">Система управления устройствами/сессиями которые вошли в ваш аккаунт и имеют к нему доступ</p>
        </footer>
    </div>
</body>
</html>

<?php
$conn->close();
?>
