<?php

/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<link rel="stylesheet" href="/public/css/styl_profile.css">

<?php
$userId = $data['user-id'] ?? -1;
$user = \App\Models\User::getOne($userId);
?>

<input type="hidden" name="user-name" value="<?= $user->getLogin() ?>"/>

<div class="card">
    <div class="field">
        <h4>Name: <?= $user->getLogin() ?></h4>
        <button type="button" class="btn btn-primary">
            <a href="<?= $link->url("home.execute", ["option-id" => 0, "user-id" => $userId]) ?>"
            >Edit</a>
        </button>
    </div>

    <div class="field">
        <h4>Email: <?= $user->getEmail() ?></h4>
        <button type="button" class="btn btn-primary">
            <a href="<?= $link->url("home.execute", ["option-id" => 1, "user-id" => $userId]) ?>"
            >Edit</a>
        </button>
    </div>

    <div class="field">
        <h4>Password: **********</h4>
        <button type="button" class="btn btn-primary">
            <a href="<?= $link->url("home.execute", ["option-id" => 2, "user-id" => $userId]) ?>"
            >Edit</a>
        </button>
    </div>

    <div class="action-buttons">
        <button type="button" class="btn btn-danger profile-btn">
            <a href="<?= $link->url("home.execute", ["option-id" => 3, "user-id" => $userId]) ?>"
            >Delete account</a>
        </button>
        <button type="button" class="btn btn-dark profile-btn">
            <a href="<?= $link->url("home.index") ?>">Back to home</a>
        </button>
    </div>
</div>
