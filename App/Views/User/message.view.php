<?php

$layout = '';
/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<link rel="stylesheet" href="/public/css/styl_message.css">

<form class="form form-message" method="post" action="<?= $link->url('home.index') ?>" style="top: 10%">
    <h2 class="title"><?= $_GET['message'] ?? '' ?></h2>

    <h2 class="message">User: <?= $_GET['name'] ?? '' ?></h2>

    <button class="btn-submit" type="submit" style="width: fit-content">Continue</button>
</form>