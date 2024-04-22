<?php

/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<link rel="stylesheet" href="/public/css/styl_home.css">
<link rel="stylesheet" href="/public/css/styl_database.css">

<?php
$min = $data['min'] ?? '';
$max = $data['max'] ?? '';
$type = $data['type'] ?? '';
$isAdmin = $data['is-admin'] ?? 0;
$missions = $data['missions'] ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="card">
            <h1>Missions database</h1>

            <form class="form" method="post">
                <div class="search">
                    <label>
                        <input class="search-field" name="min-evaluation-field" type="number"
                               placeholder="Min evaluation"
                               min="0" step="any" max="100" value="<?= $min ?>">
                    </label>

                    <label>
                        <input class="search-field" name="max-evaluation-field" type="number"
                               placeholder="Max evaluation"
                               min="0" step="any" max="100" value="<?= $max ?>">
                    </label>

                    <label>
                        <select class="search-field" name="mission-type-field">
                            <option value="">All types</option>
                            <option value="S" <?php if ($type === 'S') echo 'selected' ?>>Serial</option>
                            <option value="P" <?php if ($type === 'P') echo 'selected' ?>>Parallel</option>
                        </select>
                    </label>

                    <button class="btn btn-light" type="submit" style="height: 40px"
                            formaction="<?= $link->url("home.database", ["min" => $min, "max" => $max, "type" => $type]) ?>">
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
                        <?php if ($isAdmin) : ?>
                            <th>Action</th>
                        <?php endif ?>
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

                                <?php if ($isAdmin) : ?>
                                    <td>
                                        <button type="button" class="btn btn-danger">
                                            <a href="<?= $link->url("home.execute", ["option-id" => 4, "mission-id" => $mission['id']]) ?>">
                                                Remove
                                            </a>
                                        </button>
                                    </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                    </tbody>
                </table>

                <?php if (empty($missions)) : ?>
                    <h5 style="color: red">No results found</h5>
                <?php endif ?>
            </form>
        </div>
    </div>
</div>
