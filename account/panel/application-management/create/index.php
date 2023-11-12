<?php
require_once '../../../plugins/auth_check.php';
//require_once '../../../plugins/getting-user-data.php';

function generate_random_id() {
    return rand(1000, 9999);
}

if (!isset($isAdmin) || !$isAdmin) {
    die("Отказано в доступе");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['code']) && !empty($_POST['dateend'])) {
        $code = $_POST['code'];
        $dateend = $_POST['dateend'];
        $id = generate_random_id();

        $check_sql = "SELECT * FROM activation_codes WHERE code = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $code_exists = false;

        if ($check_result->num_rows > 0) {
            $code_exists = true;
        }

        $check_stmt->close();

        if (!$code_exists) {
            $sql = "INSERT INTO activation_codes (id, code, dateend) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id, $code, $dateend);
            $result = $stmt->execute();

            $stmt->close();
        }

        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" href="<?php echo $styleUrl; ?>">
    <title>Создание заявки</title>
</head>
<body>
<!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">Главная</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Покупки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Поддержка</a>
            </li>
        </ul>
    </div>
</nav> -->
<body>
<div class="container">
    <div class="row">
        <div class="col-12 text-center my-4">
            <!-- <h2>Добро пожаловать, $login!</h2> -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="list-group">
                <h4 class="mb-3">Управление</h4>
                <a href="../../index.php" class="list-group-item list-group-item-action">Главная</a>
                <a href="../../applications" class="list-group-item list-group-item-action">Заявки</a>
            </div>
            <div class="list-group mt-4">
                <h4 class="mb-3">Аккаунт</h4>
                <a href="../../settings" class="list-group-item list-group-item-action">Настройки</a>
                <a href="../../../logout.php" class="list-group-item list-group-item-action">Выход</a>
            </div>
                             <?php if ($isAdmin): ?>
         <div class="list-group mt-4">
        <h4 class="mb-3">Админка</h4>
        <a href="index.php" class="list-group-item list-group-item-action active">Создать заявку</a>
        <a href="../delete" class="list-group-item list-group-item-action">Удалить заявку</a>
    </div>
    <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Создание заявки</h4>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="code" class="text">Код активации</label>
                            <input type="text" name="code" id="code" placeholder="Введите любой код" class="form-control dark-blue-input" required>
                        </div>
                        <div class="form-group">
                            <label for="dateend" class="text">Дата окончания</label>
                            <input type="date" name="dateend" id="dateend" class="form-control dark-blue-input" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        // Функция для отображения уведомлений toastr
        function showToast(type, message) {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
            }
            if (type === 'success') {
                toastr.success(message);
            } else if (type === 'error') {
                toastr.error(message);
            }
        }

// Отображение уведомлений toastr при создании заявки
<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
    <?php if (isset($code_exists) && $code_exists): ?>
        showToast('error', 'Код активации уже существует. Пожалуйста, введите другой код.');
    <?php elseif (isset($result) && $result): ?>
        showToast('success', 'Заявка успешно создана!');
    <?php else: ?>
        showToast('error', 'Ошибка при создании заявки. Пожалуйста, попробуйте еще раз.');
    <?php endif; ?>
<?php endif; ?>
    });
</script>
</body>
</html>