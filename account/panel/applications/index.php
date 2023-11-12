<?php
require_once '../../plugins/auth_check.php';
// require_once '../../plugins/getting-user-data.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $styleUrl; ?>">
    <title>Главная | Заявки</title>
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
                <a href="index.php" class="list-group-item list-group-item-action active">Заявки</a>
            </div>
            <div class="list-group mt-4">
                <h4 class="mb-3">Аккаунт</h4>
                <a href="../settings" class="list-group-item list-group-item-action">Настройки</a>
                <a href="../logout.php" class="list-group-item list-group-item-action">Выход</a>
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
            <h4 class="mb-3">Список заявок</h4>
            <?php
            $sql = "SELECT * FROM applications";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            Заявка №<?= $row['id'] ?> | Код: <?= $row['code'] ?>
                        </div>
                        <div class="card-body">
                            <p>Дата рождения: <span class="bold-montserrat"><?= $row['birth_date'] ?></span></p>
                            <p>Знания правил: <span class="bold-montserrat"><?= $row['knowledge'] ?></span></p>
                            <!-- Кнопка для открытия модального окна -->
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detailsModal<?= $row['id'] ?>">Подробнее</button>
                        </div>
                    </div>

<div class="modal fade" id="detailsModal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel<?= $row['id'] ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel<?= $row['id'] ?>">Заявка №<?= $row['id'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Пример с одним полем -->
<p>Дата рождения: <span class="red bold-montserrat copy-text copy-target-birth-date-<?= $row['id'] ?>"><?= $row['birth_date'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-birth-date-<?= $row['id'] ?>"></i></p>
<p>Email: <span class="red bold-montserrat copy-target-email-<?= $row['id'] ?>"><?= $row['email'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-email-<?= $row['id'] ?>"></i></p>
<p>Никнейм на сервере: <span class="red bold-montserrat copy-target-nickname-<?= $row['id'] ?>"><?= $row['nickname'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-nickname-<?= $row['id'] ?>"></i></p>
<p>SteamID: <span class="red bold-montserrat copy-target-steamid-<?= $row['id'] ?>"><?= $row['steamid'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-steamid-<?= $row['id'] ?>"></i></p>
<p>Discord: <span class="red bold-montserrat copy-target-discord-<?= $row['id'] ?>"><?= $row['discord'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-discord-<?= $row['id'] ?>"></i></p>
<p>Часовой пояс: <span class="red bold-montserrat copy-target-time-<?= $row['id'] ?>"><?= $row['time'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-time-<?= $row['id'] ?>"></i></p>
<p>Сколько сможете уделять серверу в день минимум: <span class="red bold-montserrat copy-target-que-<?= $row['id'] ?>"><?= $row['que'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-que-<?= $row['id'] ?>"></i></p>
<p>Знания правил: <span class="red bold-montserrat copy-target-knowledge-<?= $row['id'] ?>"><?= $row['knowledge'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-knowledge-<?= $row['id'] ?>"></i></p>
<p>Почему мы должны взять именно вас?: <span class="red bold-montserrat copy-target-reason-<?= $row['id'] ?>"><?= $row['reason'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-reason-<?= $row['id'] ?>"></i></p>
<p>Сколько примерно надо тратить на одну жалобу?: <span class="red bold-montserrat copy-target-severity-<?= $row['id'] ?>"><?= $row['severity'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-severity-<?= $row['id'] ?>"></i></p>
<p>Опишите Ваши действия, если обвиняемый не реагирует на Ваши слова: <span class="red bold-montserrat copy-target-actions-<?= $row['id'] ?>"><?= $row['actions'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-actions-<?= $row['id'] ?>"></i></p>
<p>Если один из администратоов нарушит правила, каковы ваши действия: <span class="red bold-montserrat copy-target-admin_violation_actions-<?= $row['id'] ?>"><?= $row['admin_violation_actions'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-admin_violation_actions-<?= $row['id'] ?>"></i></p>
<p>На сколько вы знаете правила сервера?: <span class="red bold-montserrat copy-target-knowledge-<?= $row['id'] ?>"><?= $row['knowledge'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-knowledge-<?= $row['id'] ?>"></i></p>
<p>Дата подачи: <span class="red bold-montserrat copy-target-application_date-<?= $row['id'] ?>"><?= $row['application_date'] ?></span> <i class="fas fa-copy copy-icon" data-copy-target=".copy-target-application_date-<?= $row['id'] ?>"></i></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php
}
} else {
    echo "<p class='red'>Заявок пока нет.</p>";
}
$conn->close();
?>
</div>
</div>
</div>

<!-- Подключение jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Подключение Bootstrap и Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>

<!-- Подключение Toastr.js (для уведомлений) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

<!-- Подключение Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/js/all.min.js"></script>

<!-- Ваш скрипт копирования -->
<script>
$(document).ready(function () {
    $('.copy-icon').on('click', async function () {
        var target = $($(this).data('copy-target'));
        var text = target.text();

        if (navigator.clipboard) {
            try {
                await navigator.clipboard.writeText(text);
                toastr.success('Текст скопирован');
            } catch (err) {
                alert("Не удалось скопировать: " + text);
            }
        } else {
            alert("Ваш браузер не поддерживает Clipboard API");
        }
    });

    $('.btn-info').on('click', function () {
        $('.modal-body p span.bold-montserrat-details').removeClass('bold-montserrat-details');

        $('#detailsModal' + $(this).data('id') + ' .modal-body p span.bold-montserrat').addClass('bold-montserrat-details');
    });
});

</script>
<!-- Подключение jQuery и Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/js/all.min.js"></script>
</body>
</html>