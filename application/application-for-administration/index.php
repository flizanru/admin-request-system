<?php
$host = "localhost"; // адрес сервера базы данных
$username = "cybernuts_admin"; // имя пользователя базы данных
$password = ""; // пароль пользователя базы данных
$database = "cybernuts_admin"; // имя базы данных

// Подключаемся к базе данных
$mysqli = new mysqli($host, $username, $password, $database);

// Проверяем подключение
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Устанавливаем кодировку
$mysqli->set_charset("utf8");

$settings_query = "SELECT * FROM settings WHERE name = 'technical_works'";
$settings_result = $mysqli->query($settings_query);
$settings_data = $settings_result->fetch_assoc();

if ($settings_data['value'] == 1) {
    header("Location: technical-works.html");
    exit;
}


// Обработка формы
$error_message = '';
$success_message = '';
$is_form_valid = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Проверка кода активации
    $code = $_POST['code'];

    // Используем подготовленный запрос для выборки кода активации
    $stmt = $mysqli->prepare("SELECT * FROM activation_codes WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

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

            if ($app_result->num_rows > 0) {
                $error_message = "Заявка уже была подана";
            } else {
                $is_form_valid = true;
            }

            // Освобождаем результат запроса
            $app_result->free();
        } else {
            $error_message = "Дата активации кода истекла";
        }
    } else {
        $error_message = "Код активации неверный";
    }

    if ($is_form_valid) {
        // Обработка данных формы

$nickname = mysqli_real_escape_string($mysqli, $_POST['nickname']);
$email = mysqli_real_escape_string($mysqli, $_POST['email']);
$discord = mysqli_real_escape_string($mysqli, $_POST['discord']);
$steamid = mysqli_real_escape_string($mysqli, $_POST['steamid']);
$time = mysqli_real_escape_string($mysqli, $_POST['time']);
$que = mysqli_real_escape_string($mysqli, $_POST['que']);
$birth_date = mysqli_real_escape_string($mysqli, $_POST['birth_date']);
$reason = mysqli_real_escape_string($mysqli, $_POST['reason']);
$severity = mysqli_real_escape_string($mysqli, $_POST['severity']);
$actions = mysqli_real_escape_string($mysqli, $_POST['actions']);
$admin_violation_actions = mysqli_real_escape_string($mysqli, $_POST['admin_violation_actions']);
$knowledge = mysqli_real_escape_string($mysqli, $_POST['knowledge']);

$application_date = date('Y-m-d H:i:s');

// Используем подготовленный запрос для вставки данных в базу данных
$stmt = $mysqli->prepare("INSERT INTO applications (code, nickname, email, discord, steamid, time, que, birth_date, reason, severity, actions, admin_violation_actions, knowledge, application_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssssss", $code, $nickname, $email, $discord, $steamid, $time, $que, $birth_date, $reason, $severity, $actions, $admin_violation_actions, $knowledge, $application_date);
if ($stmt->execute()) {
    $success_message = "Заявка успешно отправлена";
} else {
    $error_message = "Ошибка базы данных при созданни, запроса. Обратитесь к администратору";
}
}

    // Закрываем подготовленный запрос и освобождаем результаты запроса
    $stmt->close();
    $result->free();

    // Закрываем соединение с базой данных
    $mysqli->close();
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Администрация CyberNutsRP</title>
    <style>
body {
  background-color: #222;
  color: #fff;
}

.container {
  max-width: 600px;
}

.error-message {
  color: red;
}

form .form-control,
form .form-select {
  background-color: #333;
  color: #fff;
  border: 1px solid #444;
  box-shadow: inset 0 -2px 0 #222;
  transition: box-shadow 0.2s ease-in-out;
}

form .form-control:focus,
form .form-select:focus {
  outline: none;
  color: #fff;
  box-shadow: inset 0 -2px 0 #007bff;
  background-color: #333; /* Сохраняем темный фон */
}

form label {
  display: inline-block;
  font-size: 1rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
  color: #fff;
}

form button[type="submit"] {
  display: inline-block;
  padding: 0.5rem 1rem;
  margin-top: 1rem;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 0.25rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s ease-in-out;
}

form button[type="submit"]:hover {
  background-color: #0062cc;
  color: #fff;
}

form .form-control::-webkit-input-placeholder,
form .form-select::-webkit-input-placeholder {
  color: #aaa;
}

form .form-control::-moz-placeholder,
form .form-select::-moz-placeholder {
  color: #aaa;
}

form .form-control:-ms-input-placeholder,
form .form-select:-ms-input-placeholder {
  color: #aaa;
}

form .form-control::placeholder,
form .form-select::placeholder {
  color: #aaa;
}

form .form-control:focus::-webkit-input-placeholder,
form .form-select:focus::-webkit-input-placeholder {
  color: #555;
}

form .form-control:focus::-moz-placeholder,
form .form-select:focus::-moz-placeholder {
  color: #555;
}

form .form-control:focus:-ms-input-placeholder,
form .form-select:focus:-ms-input-placeholder {
  color: #555;
}

form .form-control:focus::placeholder,
form .form-select:focus::placeholder {
  color: #555;
}

form .form-select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

form .form-select:focus {
  border-color: #444;
  color: #fff;
}
.form-panel {
  background-color: #444;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.25);
  padding: 1rem;
  margin-bottom: 1rem;
}
.input-icon {
    position: relative;
}

.input-icon input {
    padding-right: 40px;
}

.input-icon img {
    position: absolute;
    right: 10px;
    top: calc(70% - 10px);
    width: 20px;
    height: 20px;
}
    </style>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<div class="container mt-5">
    <h1 class="text-center mb-4">Администрация CyberNutsRP</h1>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
$(document).ready(function() {
<?php if ($error_message): ?>
    toastr.error('<?= $error_message ?>');
<?php endif; ?>

<?php if ($success_message): ?>
    toastr.success('<?= $success_message ?>');
<?php endif; ?>
});
</script>

        <form action="" method="POST">
            <div class="mb-3 form-panel">
<div class="mb-3 input-icon">
    <label for="code" class="form-label">Код активации</label>
    <input type="text" class="form-control" placeholder="1234" id="code" name="code" required>
    <img src="" alt="" id="icon" style="display:none;">
</div>

            <div class="mb-3">
                <label for="nickname" class="form-label">Ваш ник на сервере</label>
                <input type="text" class="form-control" placeholder="никнейм" id="nickname" name="nickname" required>
            </div>

<style>
    .red-text {
        color: red;
        font-size: 14px;
    }
</style>

<div class="mb-3">
    <label for="email" class="form-label">Ваш Email</label>
    <span class="red-text">Если мы захотим с вами связаться, то мы напишем вам на этот email</span>
    <input type="email" class="form-control" id="email" name="email" required>
</div>

            <div class="mb-3">
                <label for="steamid" class="form-label">Ваш SteamID</label>
                <input type="text" class="form-control" placeholder="STEAM_1:1:000000000" id="steamid" name="steamid" required>
            </div>

            <div class="mb-3">
                <label for="discord" class="form-label">Ваш Discord</label>
                <input type="text" class="form-control" placeholder="Ваш дискорд (безразницы)" id="discord" name="discord" required>
            </div>

            <div class="mb-3">
                <label for="time" class="form-label">Часовой пояс</label>
                <input type="text" class="form-control" placeholder="+3 МСК" id="time" name="time" required>
            </div>

            <div class="mb-3">
                <label for="que" class="form-label">Сколько сможете уделять серверу в день минимум</label>
                <input type="text" class="form-control" id="que" name="que" required>
            </div>

            <div class="mb-3">
                <label for="birth_date" class="form-label">Дата рождения</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Почему мы должны взять именно вас?</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="severity" class="form-label">Сколько примерно надо тратить на одну жалобу? (С демкой)</label>
                <select class="form-select" id="severity" name="severity" required>
                    <option value="">Выберите</option>
                    <option value="до5минут">До 5 минут</option>
                    <option value="больше5минут">Больше 5 минут</option>
                    <option value="Другое">Другое</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="actions" class="form-label">Опишите Ваши действия, если обвиняемый не реагирует на Ваши слова</label>
                <textarea class="form-control" id="actions" name="actions" rows="3" required></textarea>
            </div>

             <div class="mb-3">
                <label for="actions" class="form-label">Если один из администратоов нарушит правила, каковы ваши действия?</label>
                <textarea class="form-control" id="admin_violation_actions" name="admin_violation_actions" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="knowledge" class="form-label">На сколько вы знаете правила сервера?</label>
                <select class="form-select" id="knowledge" name="knowledge" required>
                    <option value="">Выберите уровень знаний</option>
                    <option value="Незнаю">Незнаю</option>
                    <option value="Низкий">Низкий</option>
                    <option value="Средний">Средний</option>
                    <option value="Высокий">Высокий</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Отправить заявку</button>
        </form>
</div>
<script>
    function checkCode() {
        const codeInput = $('#code');
        const code = codeInput.val().trim();
        const icon = $('#icon');

        if (code === '') {
            icon.hide();
            return;
        }

        $.ajax({
            url: 'check_code.php',
            method: 'POST',
            data: {code: code},
            dataType: 'json',
            success: function (response) {
                if (response.valid) {
                    icon.attr('src', 'ok.png');
                    icon.show();
                } else {
                    icon.attr('src', 'no.png');
                    icon.show();
                }
            },
            error: function () {
                toastr.error('Ошибка при проверке кода активации');
            }
        });
    }

    $('#code').on('input', function () {
        clearTimeout($.data(this, 'timer'));
        $(this).data('timer', setTimeout(checkCode, 500));
    });
</script>
</body>
</html>