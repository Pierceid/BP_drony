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

$serializedTracks = serialize($tracks);
?>

<link rel="stylesheet" href="/public/css/styl_message.css">
<link rel="stylesheet" href="/public/css/styl_home.css">

<div class="container-fluid">
    <form class="form" method="post" enctype="multipart/form-data">
        <h2 class="title">Drone mission</h2>

        <?php if ($step >= 0) : ?>
            <div class="field">
                <h5 style="margin-right: 10px">Checkpoints:</h5>
                <label>
                    <input name="checkpoints-count" type="number" step="1" min="1" max="25" value="<?= $checkpoints ?>">
                </label>

                <h5 style="margin-right: 10px; margin-left: 10px">Drones:</h5>
                <label>
                    <input name="drones-count" type="number" step="1" min="1" max="10" value="<?= $drones ?>">
                </label>

                <h5 style="margin-right: 10px; margin-left: 10px">Type:</h5>
                <label>
                    <select name="mission-type" style="padding: 4px 0">
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
                    <th style="width: 160px">Prob.\Point</th>
                    <?php for ($j = 0; $j < $checkpoints; $j++) : ?>
                        <th>P(<?= $j ?>)</th>
                    <?php endfor ?>
                </tr>
                </thead>

                <tbody>
                <?php for ($i = 0; $i < 3; $i++) : ?>
                    <tr>
                        <th>p(<?= $i ?>)</th>

                        <?php for ($j = 0; $j < $checkpoints; $j++) : ?>
                            <td>
                                <?php if ($i === 0) : ?>
                                    <label style="display:inline;">
                                        <input name="fault-point-<?= $j ?>" type="number" step="any" min="0" max="1"
                                               placeholder="P(<?= $j ?>)" value="<?= $points[$j][$i] ?? 0 ?>">
                                    </label>
                                <?php elseif ($i === 1) : ?>
                                    <label style="display:inline;">
                                        <input name="acceptable-point-<?= $j ?>" type="number" step="any" min="0"
                                               max="1" placeholder="P(<?= $j ?>)" value="<?= $points[$j][$i] ?? 0 ?>">
                                    </label>
                                <?php elseif ($i === 2) : ?>
                                    <h6 style="display:inline;"><?= $points[$j][$i] ?? 0 ?></h6>
                                <?php endif ?>
                            </td>
                        <?php endfor ?>
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
                    <th style="width: 160px">Drone\Point</th>
                    <?php for ($i = 0; $i < $checkpoints; $i++) : ?>
                        <th>P(<?= $i ?>)</th>
                    <?php endfor ?>
                </tr>
                </thead>

                <tbody>
                <?php for ($i = 0; $i < $drones; $i++) : ?>
                    <tr>
                        <th>D(<?= $i ?>)</th>

                        <?php for ($j = 0; $j < $checkpoints; $j++) : ?>
                            <td>
                                <label>
                                    <select name="has-<?= $i ?>-<?= $j ?>" style="padding: 4px 0">
                                        <option value="0" <?php echo (($tracks[$i][$j] ?? 0) === '0') ? 'selected' : '' ?>>
                                            0
                                        </option>
                                        <option value="1" <?php echo (($tracks[$i][$j] ?? 0) === '1') ? 'selected' : '' ?>>
                                            1
                                        </option>
                                    </select>
                                </label>
                            </td>
                        <?php endfor ?>
                    </tr>
                <?php endfor ?>
                </tbody>
            </table>
        <?php endif ?>

        <?php if (isset($_GET['message-rows'])) : ?>
            <h5 style="color: red">
                <?= $_GET['message-rows'] ?>
            </h5>
        <?php endif ?>

        <?php if (isset($_GET['message-columns'])) : ?>
            <h5 style="color: red">
                <?= $_GET['message-columns'] ?>
            </h5>
        <?php endif ?>

        <?php if ($step >= 3) : ?>
            <h3>Evaluation: <?= $evaluation ?> %</h3>
        <?php endif ?>

        <div class="action-buttons">
            <button class="btn-submit" type="submit" formaction="<?= $link->url('home.index') ?>">Cancel</button>
            <button class="btn-submit" type="submit" formaction="<?= $link->url($destination) ?>">Submit</button>
        </div>
    </form>
</div>

<?php if ($step >= 4) : ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['line']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Create a DataTable
            var data = new google.visualization.DataTable();
            data.addColumn('number', 'Checkpoint');
            <?php for ($i = 0; $i < count($tracks); $i++) : ?>
            data.addColumn('number', 'Drone <?= $i ?>');
            <?php endfor ?>

            // Add data rows
            <?php for ($j = 0; $j < count($tracks[0]); $j++) : ?>
            data.addRow([<?= $j + 1 ?>, <?php for ($i = 0; $i < count($tracks); $i++) : ?><?= $tracks[$i][$j] ?? 'null' ?>,<?php endfor ?>]);
            <?php endfor ?>

            // Set chart options
            var options = {
                chart: {
                    title: 'Drone Tracks',
                    subtitle: 'Checkpoints and Drone Positions'
                },
                width: Math.max(400, 300 + <?= count($tracks) * 16 ?>)
                height: 320 + <?= count($tracks) * 16 ?>
            };

            // Instantiate and draw the chart
            var chart = new google.charts.Line(document.getElementById('linechart_material'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>

    <div id="linechart_material" style="width: 100%; display: flex; align-items: center; justify-content: center; padding: 8px;"></div>
<?php endif ?>
