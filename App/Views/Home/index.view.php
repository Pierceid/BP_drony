<?php

/**
 * @var \App\Core\LinkGenerator $link
 * @var Array $data
 */
?>

<?php
$step = $_GET['step'] ?? 0;
$checkpoints = $_GET['checkpoints'] ?? 0;
$points = $_GET['points'] ?? [];
$type = $_GET['type'] ?? 'S';
$destination = $step == 0 ? 'home.checkpoints' : ($step == 1 ? 'home.probabilities' : 'home.type');
?>

<link rel="stylesheet" href="/public/css/styl_message.css">

<div class="container-fluid">
    <form class="form" method="post" enctype="multipart/form-data">
        <h2 class="title">Drone mission</h2>

        <div class="field">
            <h5 style="margin-right: 10px">Checkpoints: </h5>
            <label><input name="checkpoints-count" type="number" step="1" min="1" placeholder="T"
                          value="<?= $checkpoints ?>"></label>
        </div>

        <?php if ($step >= 1) : ?>
        <table>
            <thead>
            <tr>
                <th>Point</th>
                <th>P(0)</th>
                <th>P(1)</th>
                <th>P(2)</th>
            </tr>
            </thead>

            <tbody>
            <?php for ($i = 0; $i < $checkpoints; $i++) : ?>
                <tr>
                    <td>
                        <h6 style="display:inline;"><?= $i ?>.</h6>
                    </td>
                    <td>
                        <label style="display:inline;">
                            <input name="fault-point-<?= $i ?>" type="number" step="any" min="0" max="1"
                                   placeholder="P(0)" value="<?= $points[$i][0] ?? 0 ?>">
                        </label>
                    </td>
                    <td>
                        <label style="display:inline;">
                            <input name="acceptable-point-<?= $i ?>" type="number" step="any" min="0" max="1"
                                   placeholder="P(1)" value="<?= $points[$i][1] ?? 0 ?>">
                        </label>
                    </td>
                    <td>
                        <h6 style="display:inline;"><?= $points[$i][2] ?? 0 ?></h6>
                    </td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <?php endif ?>

        <?php if (isset($_GET['message'])) : ?>
        <h5 style="color: red">
            <?= $_GET['message'] ?>
        </h5>
        <?php endif ?>

        <?php if ($step >= 2) : ?>
        <div class="field">
            <h5 style="margin-right: 10px">Type: </h5>
            <label>
                <select name="mission-type">
                    <option value="S" <?php echo ($type === 'S') ? 'selected' : '' ?>>Serial</option>
                    <option value="P" <?php echo ($type === 'P') ? 'selected' : '' ?>>Parallel</option>
                </select>
            </label>
        </div>
        <?php endif ?>

        <div class="action-buttons">
            <button class="btn-submit" type="submit" formaction="<?= $link->url('home.index') ?>">Cancel</button>
            <button class="btn-submit" type="submit" formaction="<?= $link->url($destination) ?>">Submit</button>
        </div>
    </form>
</div>
