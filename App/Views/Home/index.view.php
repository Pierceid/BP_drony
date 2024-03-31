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
$drones = $_GET['drones'] ?? 0;
$tracks = $_GET['tracks'] ?? [];
$evaluation = $_GET['evaluation'] ?? 0;
$destination = $step == 0 ? 'home.setBaseParameters' : ($step == 1 ? 'home.setProbabilities' :
    ($step == 2 ? 'home.setTracks' : ($step == 3 ? 'home.setEvaluation' : 'home.index')));
?>

<link rel="stylesheet" href="/public/css/styl_message.css">

<div class="container-fluid">
    <form class="form" method="post" enctype="multipart/form-data">
        <h2 class="title">Drone mission</h2>

        <?php if ($step >= 0) : ?>
            <div class="field">
                <h6 style="margin-right: 10px">Checkpoints:</h6>
                <label>
                    <input name="checkpoints-count" type="number" step="1" min="1" value="<?= $checkpoints ?>">
                </label>

                <h6 style="margin-right: 10px">Drones:</h6>
                <label>
                    <input name="drones-count" type="number" step="1" min="1" max="10" value="<?= $drones ?>">
                </label>

                <h6 style="margin-right: 10px">Type:</h6>
                <label>
                    <select name="mission-type">
                        <option value="S" <?php echo ($type === 'S') ? 'selected' : '' ?>>Serial</option>
                        <option value="P" <?php echo ($type === 'P') ? 'selected' : '' ?>>Parallel</option>
                    </select>
                </label>
            </div>
        <?php endif ?>

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
                <?php endfor ?>
                </tbody>
            </table>
        <?php endif ?>

        <?php if (isset($_GET['message-points'])) : ?>
            <h5 style="color: red">
                <?= $_GET['message-points'] ?>
            </h5>
        <?php endif ?>

        <?php if ($step >= 2) : ?>
            <table>
                <thead>
                <tr>
                    <th>Drone</th>
                    <?php for ($i = 0; $i < $checkpoints; $i++) : ?>
                        <th>P(<?= $i ?>)</th>
                    <?php endfor ?>
                </tr>
                </thead>

                <tbody>
                <?php for ($i = 0; $i < $drones; $i++) : ?>
                    <tr>
                        <td>
                            <h6 style="display:inline;"><?= $i ?>.</h6>
                        </td>

                        <?php for ($j = 0; $j < $checkpoints; $j++) : ?>
                            <td>
                                <label>
                                    <select name="has-<?= $i ?>-<?= $j ?>">
                                        <option value="0" <?php echo (($tracks[$i][$j] ?? 0) === '0') ? 'selected' : '' ?>>0</option>
                                        <option value="1" <?php echo (($tracks[$i][$j] ?? 0) === '1') ? 'selected' : '' ?>>1</option>
                                    </select>
                                </label>
                            </td>
                        <?php endfor ?>
                    </tr>
                <?php endfor ?>
                </tbody>
            </table>
        <?php endif ?>

        <?php if (isset($_GET['message-tracks'])) : ?>
            <h5 style="color: red">
                <?= $_GET['message-tracks'] ?>
            </h5>
        <?php endif ?>

        <div class="action-buttons">
            <button class="btn-submit" type="submit" formaction="<?= $link->url('home.index') ?>">Cancel</button>
            <button class="btn-submit" type="submit" formaction="<?= $link->url($destination) ?>">Submit</button>
        </div>
    </form>
</div>
