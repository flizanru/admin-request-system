<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Регистрация</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gray-800">
  <div class="container mx-auto py-10">
    <div class="bg-gray-700 w-full max-w-lg rounded-lg shadow-md mx-auto p-8">
      <h3 class="text-center text-2xl font-bold mb-4 text-white">Регистрация</h3>
      <?php if (isset($_GET['error'])) : ?>
        <div class="error-panel bg-red-500 p-2 rounded mb-4 text-white">
          <?php echo $_GET['error']; ?>
        </div>
      <?php endif; ?>
      <form action="reg.php" method="POST">
        <div class="mb-4">
          <label for="login" class="block text-sm font-medium text-white">Логин:</label>
          <input type="text" id="login" name="login" placeholder="Ваш логин" value="<?php echo isset($_GET['login']) ? htmlspecialchars($_GET['login']) : ''; ?>" required
            class="form-control w-full px-3 py-2 border border-gray-600 bg-gray-600 text-white rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-white">Ваша почта:</label>
          <input type="email" id="email" name="email" placeholder="Ваша почта" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required
            class="form-control w-full px-3 py-2 border border-gray-600 bg-gray-600 text-white rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-white">Пароль:</label>
          <input type="password" id="password" name="password" placeholder="Введите пароль" required
            class="form-control w-full px-3 py-2 border border-gray-600 bg-gray-600 text-white rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="mb-4">
          <label for="confirm_password" class="block text-sm font-medium text-white">Подтвердите пароль:</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Подтвердите пароль" value="<?php echo isset($_GET['confirm_password']) ? htmlspecialchars($_GET['confirm_password']) : ''; ?>" required
            class="form-control w-full px-3 py-2 border border-gray-600 bg-gray-600 text-white rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="mb-4">
          <div class="g-recaptcha" data-sitekey="6Lf5AAspAAAAAJT7FF5BinZ-Pe4I4eazLt9i53E8"></div>
        </div>
        <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white w-full py-2 rounded">Зарегистрироваться</button>
        <p class="text-center mt-3 text-gray-200">Уже зарегистрированы? <a href="log.php" class="text-blue-400">Войти</a></p>
      </form>
    </div>
  </div>
</body>
</html>