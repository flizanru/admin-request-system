<?php
require_once '../../../plugins/auth_check.php';
//require_once '../../../plugins/getting-user-data.php';

// function generate_random_id() {
//     return rand(1000, 9999);
// }

if (!isset($isAdmin) || !$isAdmin) {
    die("Отказано в доступе");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_code'])) {
        $code_to_delete = $_POST['delete_code'];

        $delete_sql = "DELETE FROM activation_codes WHERE code = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("s", $code_to_delete);
        $delete_result = $delete_stmt->execute();

        $delete_stmt->close();
    }
}

$sql_select_codes = "SELECT * FROM activation_codes";
$result_select_codes = $conn->query($sql_select_codes);
$codes_list = [];

while ($row = $result_select_codes->fetch_assoc()) {
    array_push($codes_list, $row);
}

$conn->close();
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
    <title>Удаление</title>
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
        <a href="../create" class="list-group-item list-group-item-action">Создать заявку</a>
        <a href="index.php" class="list-group-item list-group-item-action active">Удалить заявку</a>
    </div>
    <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Список кодов</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-custom">
                        <thead>
<tr>
    <th scope="col" style="color: white;">Код активации</th>
    <th scope="col" style="color: white;">Дата окончания</th>
    <th scope="col" style="color: white;"></th>
</tr>
                        </thead>
                        <tbody>
                            <?php foreach ($codes_list as $code): ?>
                                <tr>
                                    <td><?= $code['code'] ?></td>
                                    <td><?= $code['dateend'] ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="delete_code" value="<?= $code['code'] ?>">
                                            <button type="submit" class="btn btn-danger">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <?php if (isset($delete_result) && $delete_result): ?>
                showToast('success', 'Код успешно удален!');
            <?php else: ?>
                showToast('error', 'Ошибка при удалении кода. Вероятнее код привязан к заявке');
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
</body>
</html>