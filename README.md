# admin-request-system
Проект представляет собой веб-приложение, разработанное на PHP и HTML, которое обеспечивает эффективный механизм подачи заявок.

# ДЕЙСТВИЯ ДЛЯ РАБОТЫ СКРИПТА
1. Импортируйте в вашу базу данных файл `cybernuts_admin.sql`
2. Измените подключение к базе данных в файле `config.php`
3. Измените подключение к базе данных в файлах `/account/plugins/(файлы).php`
4. В многих страницах html путь неверный поскольку система использовалась под другой сайт. Поменяйте и проверьте все страницы. (Особенно внимание на `/account/panel/index.php`)
5. Если вы всё сделали правильно то ваш скрипт должен быть успешно установлен.




# admin-request-system
This project is a web application developed using PHP and HTML, providing an efficient mechanism for submitting admin access requests.

## SETUP INSTRUCTIONS

1. Import the `cybernuts_admin.sql` file into your database.
2. Modify the database connection in the `config.php` file.
3. Update the database connection in the files under `/account/plugins/`.
4. Verify and correct the paths in many HTML pages, as the system was previously used for a different website. Pay special attention to `/account/panel/index.php`.
5. If you have completed all steps correctly, your script should be successfully installed.
