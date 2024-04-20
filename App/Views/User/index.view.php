<?php

$layout = '';
/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<?php
$option = $_GET['option'] ?? '';
$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$signUpMessage = $_GET['sign-up-message'] ?? '';
$signInMessage = $_GET['sign-in-message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Drone Missions</title>

    <link rel="stylesheet" href="/public/css/styl_user.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
</head>

<body>

<input id="option" type="hidden" value="<?= $option ?>"/>

<div class="container <?= $option == 2 ? 'register-mode' : '' ?>">
    <div class="form-container sign-up">
        <form class="form-sign-up" method="post" action="<?= $link->url("user.checkRegister") ?>">
            <h1 class="title">Register</h1>

            <span>or use your email for registration</span>

            <label><input name="sign-up-name" type="text" placeholder="Username" value="<?= $name ?>"></label>

            <label><input name="sign-up-email" type="email" placeholder="Email" value="<?= $email ?>"></label>

            <label><input name="sign-up-password" type="password" minlength="6" placeholder="Password"></label>

            <?php if (!empty($option) && !empty($signUpMessage)) : ?>
                <h6 id="sign-up-message" class="error-message"><?= $signUpMessage ?></h6>
            <?php endif ?>
            <button name="sign-up-btn" class="btn-submit" type="submit">Sign up</button>
        </form>
    </div>

    <div class="form-container sign-in">
        <form class="form-sign-in" method="post" action="<?= $link->url("user.checkLogin") ?>">
            <h1 class="title">Log in</h1>

            <span>or use your email and password</span>

            <label><input name="sign-in-email" type="email" placeholder="Email" value="<?= $email ?>"></label>

            <label><input name="sign-in-password" type="password" minlength="6" placeholder="Password"></label>

            <?php if (!empty($option) && !empty($signInMessage)) : ?>
                <h6 id="sign-in-message" class="error-message"><?= $signInMessage ?></h6>
            <?php endif ?>

            <a href="#">Forgot your password?</a>

            <button name="sign-in-btn" class="btn-submit" type="submit">Sign in</button>
        </form>
    </div>

    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel left-panel">
                <h1 class="title">Welcome back!</h1>
                <p>Enter your personal details to use all site features.</p>
                <button class="btn-hidden" id="login">Sign in</button>
                <img src="/public/images/pizza-sharing.png" alt="">
            </div>

            <div class="toggle-panel right-panel">
                <h1 class="title">New here?</h1>
                <p>Register with your personal details to use all site features.</p>
                <button class="btn-hidden" id="register">Sign up</button>
                <img src="/public/images/welcome.png" alt="">
            </div>
        </div>
    </div>
</div>

<script src="/public/js/script_user.js"></script>
</body>
</html>
