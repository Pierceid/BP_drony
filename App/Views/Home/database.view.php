<?php

/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<link rel="stylesheet" href="/public/css/styl_database.css">

<?php
$missions = $data['missions'] ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="card">
            <h1>Users table</h1>

            <form class="form" method="post">
                <div class="search">
                    <input class="search-field" name="login-field" type="search" placeholder="Login"
                           aria-label="Search">
                    <input class="search-field" name="email-field" type="search" placeholder="Email"
                           aria-label="Search">
                    <label>
                        <select class="search-field" name="is-admin-field">
                            <option value=""></option>
                            <option value="0">Successful</option>
                            <option value="1">Unsuccessful</option>
                        </select>
                    </label>
                    <button class="btn btn-light" type="submit" formaction="<?= $link->url("home.database") ?>">
                        Search
                    </button>
                </div>

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Drones</th>
                        <th>Checkpoints</th>
                        <th>Type</th>
                        <th>Evaluation</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($missions)) : ?>
                        <?php foreach ($missions as $mission): ?>
                            <tr>
                                <td><?= $mission['id'] ?></td>
                                <td><?= $mission['drones'] ?></td>
                                <td><?= $mission['checkpoints'] ?></td>
                                <td><?= $mission['type'] ?></td>
                                <td><?= $mission['evaluation'] ?></td>

                                <td>
                                    <button type="button" class="btn btn-danger">
                                        <a href="<?= $link->url("home.execute", ["option-id" => 4, "mission-id" => $mission['id']]) ?>">
                                            Remove
                                        </a>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                    </tbody>
                </table>

                <?php if (empty($missions)) : ?>
                    <h5 style="color: red">0 results found</h5>
                <?php endif ?>
            </form>
        </div>
    </div>
</div>
