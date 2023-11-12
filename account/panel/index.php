<?php
require_once '../plugins/auth_check.php';
// require_once '../plugins/getting-user-data.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="<?php echo $styleUrl; ?>">
    <title>Главная | Личный Кабинет</title>
</head>
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
                <a href="index.php" class="list-group-item list-group-item-action active">Главная</a>
                <a href="applications" class="list-group-item list-group-item-action">Заявки</a>
            </div>
            <div class="list-group mt-4">
                <h4 class="mb-3">Аккаунт</h4>
                <a href="settings" class="list-group-item list-group-item-action">Настройки</a>
                <a href="../logout.php" class="list-group-item list-group-item-action">Выход</a>
            </div>
            <?php if ($isAdmin): ?>
                <div class="list-group mt-4">
                    <h4 class="mb-3">Админка</h4>
                    <a href="application-management/create" class="list-group-item list-group-item-action">Создать заявку</a>
                    <a href="application-management/delete" class="list-group-item list-group-item-action">Удалить заявку</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="panel">
                <div class="panel-header">
                    <h4>Информация о вашем аккаунте</h4>
                </div>
                <div class="panel-body">
                    <span>ID аккаунта: </span><span class="text"><?php echo $userId; ?></span></p>
                    <span>E-Mail: </span><span class="text"><?php echo $email; ?></span></p>
                    <span>Логин: </span><span class="text"><?php echo $userLogin; ?></span></p>
                    <?php
                    if ($isAdmin) {
                        echo '<span>Доступ к созданию заявок: </span><span class="text" style="color: green;">Доступ выдан</span></p>';
                    } else {
                        echo '<span>Доступ к созданию заявок: </span><span class="text" style="color: red;">Доступ запрещён</span</p>';
                    }
                    ?>
                    <?php
                    if ($isAdmin2) {
                        echo '<span>Доступ созданию пользователей: </span><span class="text" style="color: green;">Доступ выдан</span></p>';
                    } else {
                        echo '<span>Доступ созданию пользователей: </span><span class="text" style="color: red;">Доступ запрещён</span></p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSS_GFpoO/+/+0XeQxjzOUlujF9FXpXl1t8PsZf+4Pp5y+Kv4" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoJtKh7z7lGz7fuP4F8nfdFvAOA6Gg/z6Y5J6XqqyGXYM2ntX" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js" integrity="sha384-+g7LkWEck3gFs+j7J/7Owy+aH+Q4p8FkedYko6D5B6F2p+L/5DQfOayy+7" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>
