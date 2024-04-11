<?php
$step = $_GET['step'] ?? 0;
$checkpoints = $_GET['checkpoints'] ?? 0;
$drones = $_GET['drones'] ?? 0;
$tracks = $_GET['tracks'] ?? '';

$width = 600;
$height = 400;
$padding = 50;
$image = imagecreate($width, $height);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$red = imagecolorallocate($image, 255, 0, 0);
$green = imagecolorallocate($image, 0, 255, 0);

imagefilledrectangle($image, 0, 0, $width, $height, $white);
imageline($image, $padding, $height - $padding, $padding, $padding, $black);  // Y-axis
imageline($image, $padding, $height - $padding, $width - $padding, $height - $padding, $black);  // X-axis

// Draw each drone's track
$colors = [$red, $green];
$droneCount = count($tracks);
$checkpoints = count($tracks[0]);
$checkpointInterval = ($width - 2 * $padding) / ($checkpoints - 1);
for ($i = 0; $i < $droneCount; $i++) {
    $track = $tracks[$i];
    $color = $colors[$i % count($colors)];  // Alternate colors for each drone
    $prevX = null;
    $prevY = null;
    for ($j = 0; $j < $checkpoints; $j++) {
        $x = $padding + $j * $checkpointInterval;
        $y = $height - $padding - $track[$j] * ($height - 2 * $padding);
        if ($prevX !== null && $prevY !== null) {
            imageline($image, $prevX, $prevY, $x, $y, $color);  // Draw line segment
        }
        $prevX = $x;
        $prevY = $y;
    }
}

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>
