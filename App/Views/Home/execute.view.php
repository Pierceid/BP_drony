<?php

/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<link rel="stylesheet" href="/public/css/styl_home.css">
<link rel="stylesheet" href="/public/css/styl_message.css">

<?php
$optionId = $_GET['option-id'] ?? '';
$userId = $_GET['user-id'] ?? '';
$missionId = $_GET['mission-id'] ?? '';

$user = !empty($userId) ? \App\Models\User::getOne($userId) : null;
$name = !is_null($user) ? $user->getLogin() : '';
$email = !is_null($user) ? $user->getEmail() : '';
$destination = $optionId == 4 ? 'home.database' : 'home.profile';
$button = $optionId == 3 || $optionId == 4 ? 'Delete' : 'Edit';
$header = $optionId == 0 ? 'Edit name' : ($optionId == 1 ? 'Edit email' : ($optionId == 2 ? 'Edit password' :
    ($optionId == 3 ? 'Delete account' : ($optionId == 4 ? 'Delete mission' : ''))));
?>

<form class="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="option-id" value="<?= $optionId ?>"/>
    <input type="hidden" name="user-id" value="<?= $userId ?>"/>
    <input type="hidden" name="mission-id" value="<?= $missionId ?>"/>

    <h2 class="title"><?= $header ?></h2>

    <label>
        <?php if ($optionId == 0) : ?>
            <input name="name" type="text" placeholder="Name" value="<?= $name ?>">
        <?php elseif ($optionId == 1) : ?>
            <input name="email" type="email" placeholder="Email" value="<?= $email ?>">
        <?php elseif ($optionId == 2) : ?>
            <input name="password-old" type="password" minlength="6" placeholder="Old password">
            <input name="password-new" type="password" minlength="6" placeholder="New password">
        <?php elseif ($optionId == 3) : ?>
            <h5>Are you sure you want to delete this account?</h5>
        <?php elseif ($optionId == 4) : ?>
            <h5>Are you sure you want to delete this mission?</h5>
        <?php endif ?>
    </label>

    <?php if (isset($_GET['message'])) : ?>
        <h5 style="color: <?= str_contains($_GET['message'], 'Failed') ? 'red' : 'green' ?>; margin: 10px 0">
            <?= $_GET['message'] ?>
        </h5>
    <?php endif ?>

    <div class="action-buttons">
        <button class="btn-submit" type="submit" formaction="<?= $link->url($destination) ?>">Cancel</button>
        <button class="btn-submit" type="submit"
                formaction="<?= $link->url("home.executeOperation") ?>"><?= $button ?></button>
    </div>
</form>
