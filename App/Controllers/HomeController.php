<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;

/**
 * Class HomeController
 * Example class of a controller
 * @package App\Controllers
 */
class HomeController extends AControllerBase
{
    /**
     * Authorize controller actions
     * @param $action
     * @return bool
     */
    public function authorize($action)
    {
        return true;
    }

    /**
     * Example of an action accessible without authorization
     * @return \App\Core\Responses\ViewResponse
     */
    public function contact(): Response
    {
        return $this->html();
    }

    /**
     * Example of an action (authorization needed)
     * @return \App\Core\Responses\Response|\App\Core\Responses\ViewResponse
     */
    public function index(): Response
    {
        return $this->html();
    }

    public function checkpoints(): Response
    {
        $formData = $this->app->getRequest();
        $checkpoints = $formData->getValue("checkpoints-count");

        $data = ["step" => 1, "checkpoints" => $checkpoints];
        return $this->redirect($this->url("home.index", $data));
    }

    public function probabilities(): Response
    {
        $points = $this->validateCheckpoints();

        if (!empty($points['invalidPoints'])) {
            $message = "Invalid points: " . implode(', ', $points['invalidPoints']) . "\n";
            $data = ["step" => 1, "checkpoints" => $points['checkpoints'], "points" => $points['points'], "message" => $message];
            return $this->redirect($this->url("home.index", $data));
        }

        $data = ["step" => 2, "checkpoints" => $points['checkpoints'], "points" => $points['points']];
        return $this->redirect($this->url("home.index", $data));
    }

    public function type(): Response
    {
        $points = $this->validateCheckpoints();

        if (!empty($points['invalidPoints'])) {
            $message = "Invalid points: " . implode(', ', $points['invalidPoints']) . "\n";
            $data = ["step" => 2, "checkpoints" => $points['checkpoints'], "points" => $points['points'], "message" => $message];
            return $this->redirect($this->url("home.index", $data));
        }

        $formData = $this->app->getRequest();
        $type = $formData->getValue("mission-type");
        $data = ["step" => 3, "checkpoints" => $points['checkpoints'], "points" => $points['points'], "type" => $type];
        return $this->redirect($this->url("home.index", $data));
    }

    private function validateCheckpoints(): array
    {
        $formData = $this->app->getRequest();
        $checkpoints = $formData->getValue("checkpoints-count");
        $points = [];
        $invalidPoints = [];

        for ($i = 0; $i < $checkpoints; $i++) {
            $points[$i][0] = (double) ($formData->getValue("fault-point-$i") ?? 0);
            $points[$i][1] = (double) ($formData->getValue("acceptable-point-$i") ?? 0);
            $points[$i][2] = 1 - $points[$i][0] - $points[$i][1];
            $sum = $points[$i][0] + $points[$i][1] + $points[$i][2];

            if ($sum != 1 || $points[$i][2] < 0) {
                $invalidPoints[] = $i;
            }
        }

        return ['points' => $points, 'invalidPoints' => $invalidPoints, 'checkpoints' => $checkpoints];
    }
}
