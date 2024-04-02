<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Models\Mission;
use App\Models\User;

class HomeController extends AControllerBase
{
    public function index(): Response
    {
        return $this->html();
    }

    public function profile(): Response
    {
        $data = ["user-id" => $this->findUser()->getId()];
        return $this->html($data);
    }

    public function database(): Response
    {
        $data = ["missions" => []];
        return $this->html($data);
    }

    public function execute(): Response
    {
        $formData = $this->app->getRequest();
        $optionId = $formData->getValue("option-id");
        $userId = $formData->getValue("user-id");
        $missionId = $formData->getValue("mission-id");
        $data = ["option-id" => $optionId, "user-id" => $userId, "mission-id" => $missionId];
        return $this->html($data);
    }

    public function executeOperation(): Response
    {
        $formData = $this->app->getRequest();
        $optionId = $formData->getValue("option-id");
        $userId = $formData->getValue("user-id");
        $missionId = $formData->getValue("mission-id");
        $nameNew = $formData->getValue("name");
        $emailNew = $formData->getValue("email");
        $passwordOld = $formData->getValue("password-old");
        $passwordNew = $formData->getValue("password-new");
        $destination = $optionId == "3" ? "user.index" : ($optionId == "4" ? "home.database" : "home.execute");

        $message = match ($optionId) {
            "0" => $this->handleInput(nameNew: $nameNew),
            "1" => $this->handleInput(emailNew: $emailNew),
            "2" => $this->handleInput(passwordOld: $passwordOld, passwordNew: $passwordNew),
            "3" => $this->removeAccount(userId: $userId),
            "4" => $this->removeMission(missionId: $missionId),
            default => "Invalid option!",
        };

        $data = ["option-id" => $optionId, "user-id" => $userId, "mission-id" => $missionId, "message" => $message];
        return $this->redirect($this->url($destination, $data));
    }

    public function removeAccount($userId): Response
    {
        $user = User::getOne($userId);
        $user->delete();

        return $this->redirect($this->url('user.index'));
    }

    public function removeMission($missionId): Response
    {
        $mission = Mission::getOne($missionId);
        $mission->delete();

        return $this->redirect($this->url('home.database'));
    }

    private function handleInput($nameNew = null, $emailNew = null, $passwordOld = null, $passwordNew = null): string
    {
        $currentUser = $this->findUser();

        if (!is_null($nameNew)) {
            return $this->validateName($currentUser, $nameNew);
        } elseif (!is_null($emailNew)) {
            return $this->validateEmail($currentUser, $emailNew);
        } elseif (!is_null($passwordOld) && !is_null($passwordNew)) {
            return $this->validatePassword($currentUser, $passwordOld, $passwordNew);
        } else {
            return "";
        }
    }

    private function validateName($currentUser, $nameNew): string
    {
        $users = User::getAll();
        $existingUser = array_filter($users, function ($user) use ($currentUser, $nameNew) {
            return $user !== $currentUser && $user->getLogin() == $nameNew;
        });

        if (!empty($existingUser) || $nameNew == $currentUser->getLogin() || empty($nameNew) || strlen($nameNew) > 200) {
            return "Failed to update your name!";
        }

        $currentUser->setLogin($nameNew);
        $currentUser->save();
        $_SESSION['user'] = $nameNew;
        return "Your name has been successfully updated!";
    }

    private function validateEmail($currentUser, $emailNew): string
    {
        $users = User::getAll();
        $existingUser = array_filter($users, function ($user) use ($currentUser, $emailNew) {
            return $user !== $currentUser && $user->getEmail() == $emailNew;
        });

        if (!empty($existingUser) || $emailNew == $currentUser->getEmail() || empty($emailNew) || strlen($emailNew) > 200) {
            return "Failed to update your email!";
        }

        $currentUser->setEmail($emailNew);
        $currentUser->save();
        return "Your email has been successfully updated!";
    }

    private function validatePassword($currentUser, $passwordOld, $passwordNew): string
    {
        if (empty($passwordOld) || !password_verify($passwordOld, $currentUser->getPassword()) ||
            empty($passwordNew) || strlen($passwordNew) > 200 || $passwordNew == $passwordOld) {
            return "Failed to update your password!";
        }

        $currentUser->setPassword(password_hash($passwordNew, PASSWORD_DEFAULT));
        $currentUser->save();
        return "Your password has been successfully updated!";
    }

    public function setBaseParameters(): Response
    {
        $formData = $this->app->getRequest();
        $checkpoints = $formData->getValue("checkpoints-count");
        $drones = $formData->getValue("drones-count");
        $type = $formData->getValue("mission-type");

        $data = [
            "step" => 1,
            "checkpoints" => $checkpoints,
            "drones" => $drones,
            "type" => $type
        ];

        return $this->redirect($this->url("home.index", $data));
    }

    public function setProbabilities(): Response
    {
        $formData = $this->app->getRequest();
        $drones = $formData->getValue("drones-count");
        $type = $formData->getValue("mission-type");
        $points = $this->validateCheckpoints();

        if (!empty($points['invalidPoints'])) {
            $messagePoints = "Invalid points: " . implode(', ', $points['invalidPoints']) . "\n";
            $data = [
                "step" => 2,
                "checkpoints" => $points['checkpoints'],
                "drones" => $drones,
                "type" => $type,
                "points" => $points['points'],
                "message-points" => $messagePoints
            ];

            return $this->redirect($this->url("home.index", $data));
        }

        $data = [
            "step" => 2,
            "checkpoints" => $points['checkpoints'],
            "drones" => $drones,
            "type" => $type,
            "points" => $points['points']
        ];

        return $this->redirect($this->url("home.index", $data));
    }

    public function setTracks(): Response
    {
        $formData = $this->app->getRequest();
        $type = $formData->getValue("mission-type");
        $points = $this->validateCheckpoints();
        $tracks = $this->validateTracks();

        if (!empty($points['invalidPoints'] || !empty($tracks['invalidRows']) || !empty($tracks['invalidColumns']))) {
            $messagePoints = !empty($points['invalidPoints']) ?
                "Invalid points: " . implode(', ', $points['invalidPoints']) . "\n" : "";
            $messageRows = !empty($tracks['invalidRows']) ?
                "Invalid rows: " . implode(', ', $tracks['invalidRows']) . "\n" : "";
            $messageColumns = !empty($tracks['invalidColumns']) ?
                "Invalid columns: " . implode(', ', $tracks['invalidColumns']) . "\n" : "";

            $data = [
                "step" => 2,
                "checkpoints" => $points['checkpoints'],
                "points" => $points['points'],
                "type" => $type,
                "drones" => $tracks['drones'],
                "tracks" => $tracks['tracks'],
                "message-points" => $messagePoints,
                "message-rows" => $messageRows,
                "message-columns" => $messageColumns
            ];

            return $this->redirect($this->url("home.index", $data));
        }

        $data = [
            "step" => 3,
            "checkpoints" => $points['checkpoints'],
            "points" => $points['points'],
            "type" => $type,
            "drones" => $tracks['drones'],
            "tracks" => $tracks['tracks']
        ];

        return $this->redirect($this->url("home.index", $data));
    }

    public function setEvaluation(): Response
    {
        $formData = $this->app->getRequest();
        $type = $formData->getValue("mission-type");
        $points = $this->validateCheckpoints();
        $tracks = $this->validateTracks();

        if (!empty($points['invalidPoints'] || !empty($tracks['invalidRows']) || !empty($tracks['invalidColumns']))) {
            $messagePoints = !empty($points['invalidPoints']) ?
                "Invalid points: " . implode(', ', $points['invalidPoints']) . "\n" : "";
            $messageRows = !empty($tracks['invalidRows']) ?
                "Invalid rows: " . implode(', ', $tracks['invalidRows']) . "\n" : "";
            $messageColumns = !empty($tracks['invalidColumns']) ?
                "Invalid columns: " . implode(', ', $tracks['invalidColumns']) . "\n" : "";

            $data = [
                "step" => 2,
                "checkpoints" => $points['checkpoints'],
                "points" => $points['points'],
                "type" => $type,
                "drones" => $tracks['drones'],
                "tracks" => $tracks['tracks'],
                "message-points" => $messagePoints,
                "message-rows" => $messageRows,
                "message-columns" => $messageColumns
            ];

            return $this->redirect($this->url("home.index", $data));
        }

        $evaluation = $this->calculateMissionReliability($tracks['drones'], $tracks['tracks'], $points['points']);

        $data = [
            "step" => 3,
            "checkpoints" => $points['checkpoints'],
            "points" => $points['points'],
            "type" => $type,
            "drones" => $tracks['drones'],
            "tracks" => $tracks['tracks'],
            "evaluation" => $evaluation
        ];

        return $this->redirect($this->url("home.index", $data));
    }

    private function validateCheckpoints(): array
    {
        $formData = $this->app->getRequest();
        $checkpoints = $formData->getValue("checkpoints-count");
        $points = [];
        $invalidPoints = [];

        for ($i = 0; $i < $checkpoints; $i++) {
            $points[$i][0] = (double)($formData->getValue("fault-point-$i") ?? 0);
            $points[$i][1] = (double)($formData->getValue("acceptable-point-$i") ?? 0);
            $points[$i][2] = 1 - $points[$i][0] - $points[$i][1];
            $sum = $points[$i][0] + $points[$i][1] + $points[$i][2];

            if (round($sum) != 1 || $points[$i][2] < 0) {
                $invalidPoints[] = $i;
            }
        }

        return ['points' => $points, 'invalidPoints' => $invalidPoints, 'checkpoints' => $checkpoints];
    }

    private function validateTracks(): array
    {
        $formData = $this->app->getRequest();
        $type = $formData->getValue("mission-type");
        $drones = $formData->getValue("drones-count");
        $checkpoints = $formData->getValue("checkpoints-count");
        $tracks = [];
        $invalidRows = [];
        $invalidColumns = [];

        if ($type == "S") {
            for ($i = 0; $i < $checkpoints; $i++) {
                $sum = 0;

                for ($j = 0; $j < $drones; $j++) {
                    $tracks[$j][$i] = $formData->getValue("has-$j-$i") ?? 0;
                    $sum += (int)($tracks[$j][$i] ?? 0);
                }

                if ($sum <= 0) {
                    $invalidColumns[] = $i;
                }
            }
        }

        for ($i = 0; $i < $drones; $i++) {
            $sum = 0;

            for ($j = 0; $j < $checkpoints; $j++) {
                $tracks[$i][$j] = $formData->getValue("has-$i-$j") ?? 0;
                $sum += (int)($tracks[$i][$j] ?? 0);
            }

            if ($sum <= 0) {
                $invalidRows[] = $i;
            }
        }

        return ['tracks' => $tracks, 'invalidRows' => $invalidRows, 'invalidColumns' => $invalidColumns, 'drones' => $drones];
    }

    public function calculateMissionReliability($drones, $tracks, $points): float
    {
        $R = [];
        $commonCheckpoints = $this->findCommonCheckpoints($tracks);

        for ($i = 0; $i < $drones; $i++) {
            if (in_array($tracks[$i], $commonCheckpoints)) {
                $random = mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX;
                $weight = $random < $points[$i][0] ? $points[$i][0] :
                    ($random < $points[$i][0] + $points[$i][1] ? $points[$i][1] : $points[$i][2]);
                $R[] = $weight;
            }
        }

        if (!empty($R)) {
            $R_common = max($R);
            $R_series = 1.0;

            for ($i = 0; $i < $drones; $i++) {
                if (!in_array($tracks[$i], $commonCheckpoints)) {
                    $random = mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX;
                    $weight = $random < $points[$i][0] ? $points[$i][0] :
                        ($random < $points[$i][0] + $points[$i][1] ? $points[$i][1] : $points[$i][2]);
                    $R_series *= $weight;
                }
            }

            return $R_common * $R_series;
        }

        $R_series = 0.0;
        for ($i = 0; $i < $drones; $i++) {
            $random = mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX;
            $weight = $random < $points[$i][0] ? $points[$i][0] :
                ($random < $points[$i][0] + $points[$i][1] ? $points[$i][1] : $points[$i][2]);
            $R_series += $weight;
        }

        return $R_series;
    }

    public function findCommonCheckpoints($tracks)
    {
        $common = $tracks[0];

        for ($i = 1; $i < count($tracks); $i++) {
            $common = array_intersect($common, $tracks[$i]);
        }

        return $common;
    }

    private function findUser(): ?User
    {
        $users = User::getAll();
        foreach ($users as $user) {
            if ($user->getLogin() == $this->app->getAuth()->getLoggedUserName()) {
                return $user;
            }
        }
        return null;
    }
}