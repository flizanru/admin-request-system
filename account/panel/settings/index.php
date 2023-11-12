<?php

require_once '../../plugins/auth_check.php';
//require_once '../../plugins/getting-user-data.php';
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
    <title>Настройки</title>
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
                <a href="../index.php" class="list-group-item list-group-item-action">Главная</a>
                <a href="../applications" class="list-group-item list-group-item-action">Заявки</a>
            </div>
            <div class="list-group mt-4">
                <h4 class="mb-3">Аккаунт</h4>
                <a href="index.php" class="list-group-item list-group-item-action active">Настройки</a>
                <a href="../../logout.php" class="list-group-item list-group-item-action">Выход</a>
            </div>
                 <?php if ($isAdmin): ?>
         <div class="list-group mt-4">
        <h4 class="mb-3">Админка</h4>
        <a href="../application-management/create" class="list-group-item list-group-item-action">Создать заявку</a>
        <a href="../application-management/delete" class="list-group-item list-group-item-action">Удалить заявку</a>
    </div>
    <?php endif; ?>
        </div>
        <div class="col-md-8">
            <h2>Настройки</h2>
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Авторизация</h4>
                </div>
                <div class="card-body">
                    <a href="session-management" class="btn btn-primary">Открыть управление устройствами</a>
                </div>
            </div>
            <form name="changePasswordForm" method="post" action="change_password.php">
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Пароль</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="changePasswordCheckbox" onchange="togglePasswordFields()">
                            <label class="form-check-label" for="changePasswordCheckbox">Изменить пароль</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="oldPasswordInput">Старый пароль:</label>
                        <input type="password" class="form-control dark-blue-input" id="oldPasswordInput" placeholder="Ваш старый пароль" name="oldPasswordInput" disabled>
                    </div>
                    <div class="form-group">
                        <label for="newPasswordInput">Новый пароль:</label>
                        <input type="password" class="form-control dark-blue-input" id="newPasswordInput" placeholder="Ваш новый пароль" name="newPasswordInput" disabled>
                    </div>
                    <div class="form-group">
                        <label for="confirmPasswordInput">Повторно новый пароль:</label>
                        <input type="password" class="form-control dark-blue-input" id="confirmPasswordInput" placeholder="Повторно новый пароль" name="confirmPasswordInput" disabled>
                    </div>
                    <button class="btn btn-primary" id="changePasswordButton">Изменить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha384-ZvpUoO/+Pzj+KryoMmUz5l/6en8XCp+HHAAK5GSLf2xlYtvJ8U2Q4U+9cuEnJoa3"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"
        integrity="sha384-eMNCOe7tC1doHpGoJtKh7z7lGz7fuP4F8nfdFvAOA6Gg/z6Y5J6XqqyGXYM2ntX"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"
        integrity="sha384-+g7LkWEck3gFs+j7J/7Owy+aH+Q4p8FkedYko6D5B6F2p+L/5DQfOayy+7"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const changePasswordButton = document.getElementById("changePasswordButton");
        changePasswordButton.addEventListener("click", changePassword);
    });

    function togglePasswordFields() {
        var changePasswordCheckbox = document.getElementById('changePasswordCheckbox');
        var oldPasswordInput = document.getElementById('oldPasswordInput');
        var newPasswordInput = document.getElementById('newPasswordInput');
        var confirmPasswordInput = document.getElementById('confirmPasswordInput');

        oldPasswordInput.disabled = !changePasswordCheckbox.checked;
        newPasswordInput.disabled = !changePasswordCheckbox.checked;
        confirmPasswordInput.disabled = !changePasswordCheckbox.checked;
    }

    async function changePassword() {
        console.log("changePassword called");

        const oldPassword = document.getElementById("oldPasswordInput").value;
        const newPassword = document.getElementById("newPasswordInput").value;
        const confirmPassword = document.getElementById("confirmPasswordInput").value;

        if (newPassword !== confirmPassword) {
            showToast("Новые пароли не совпадают. Пожалуйста, проверьте и попробуйте снова.", 'error');
            return;
        }

        const response = await fetch("change_password.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                oldPassword: oldPassword,
                newPassword: newPassword
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast("Пароль успешно изменен!", 'success');
        } else {
            showToast("Не удалось изменить пароль: " + result.error, 'error');
            console.error("Не удалось изменить пароль: " + result.error);
        }
    }

    function showToast(message, type) {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: type === 'success' ? 'green' : 'red',
            stopOnFocus: true
        }).showToast();
    }
</script>
</body>
</html>
