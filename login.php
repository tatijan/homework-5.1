<?php
$login = isset($_POST['login']) ? trim($_POST['login']) : false;
$password = isset($_POST['password']) ? $_POST['password'] : false;
$message = "Введите данные для регистрации или войдите, если уже регистрировались:";
if (isset($_POST['register'])) {
    if (!empty($login) && !empty($password)) {
//        $securePassword = md5($password);
        $securePassword = $password;
        $sql = "SELECT login FROM user WHERE login = ?";
        $stm = $db->prepare($sql);
        $stm->execute([
            $login
        ]);
        if (empty($stm->fetchColumn())) {
            $sql = "INSERT INTO user (login, password) VALUES (?, ?)";
            $stm = $db->prepare($sql);
            $stm->execute([
                $login,
                $securePassword
            ]);
            login($login);
        } else {
            $message = "Такой пользователь уже существует в базе данных.";
        }
    } else {
        $message = "Ошибка регистрации. Введите все необхдоимые данные.";
    }
}
if (isset($_POST['sign_in'])) {
    if (!empty($login) && !empty($password)) {
//        $securePassword = md5($password);
        $securePassword = $password;
        $sql = "SELECT login FROM user WHERE login = ? AND password = ?";
        $stm = $db->prepare($sql);
        $stm->execute([
            $login,
            $securePassword
        ]);
        if (!empty($stm->fetchColumn())) {
            login($login);
        } else {
            $message = "Такой пользователь не существует, либо неверный пароль.";
        }
    } else {
        $message = "Ошибка входа. Введите все необхдоимые данные.";
    }
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Авторизация</title>
</head>
<body>
<h1>Мы рады видеть Вас на нашем сайте!</h1>
<form action="" method="post">
    <div>
        <p><?=$message?></p>
        <label for="lg">Введите ваше имя</label>
        <input type="text" placeholder="Имя" name="login" id="lg" required>
    </div>
    <div>
        <label for="key">Пароль</label>
        <input type="password"  placeholder="Пароль" name="password" id="key" required>
    </div>
    <input type="submit" name="sign_in" value="Вход">
    <input type="submit" name="register" value="Регистрация">
</form>
</body>
</html>