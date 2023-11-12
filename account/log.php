<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Авторизация</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gray-800">
  <div class="container mx-auto min-h-screen flex justify-center items-center">
    <div class="bg-gray-700 w-full max-w-lg rounded-lg shadow-md">
      <div class="px-8 py-6">
        <h3 class="text-2xl font-semibold mb-6 text-white">Авторизация</h3>
        <?php if (isset($_GET['error'])) : ?>
          <div class="error-panel bg-red-500 p-2 rounded mb-4 text-white">
            <?php echo $_GET['error']; ?>
          </div>
        <?php endif; ?>
        <form action="logi.php" method="POST">
          <div class="mb-4">
            <label for="email" class="block text-gray-200 mb-1">Email:</label>
            <input type="email" class="form-control w-full px-3 py-2 border border-gray-600 bg-gray-600 text-white rounded" id="email" name="email" placeholder="Ваш email" required>
          </div>
          <div class="mb-4">
            <label for="password" class="block text-gray-200 mb-1">Пароль:</label>
            <input type="password" class="form-control w-full px-3 py-2 border border-gray-600 bg-gray-600 text-white rounded" id="password" name="password" placeholder="Введите пароль" required>
          </div>
        <div class="mb-4">
          <div class="g-recaptcha" data-sitekey="6Lf5AAspAAAAAJT7FF5BinZ-Pe4I4eazLt9i53E8"></div>
        </div>
          <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white w-full py-2 rounded">Войти</button>
          <p class="text-center mt-3 text-gray-200">Вы не зарегистрированы? <a href="register.php" class="text-blue-400">Зарегистрироваться</a></p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>