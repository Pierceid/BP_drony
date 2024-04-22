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
        $formData = $this->app->getRequest();
        $data["min"] = $formData->getValue("min-evaluation-field");
        $data["max"] = $formData->getValue("max-evaluation-field");
        $data["type"] = $formData->getValue("mission-type-field");
        $data["is-admin"] = $this->findUser()->getIsAdmin();
        $data["missions"] = $this->getMissions();
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
        $data = [
            "option-id" => $optionId,
            "user-id" => $userId,
            "mission-id" => $missionId,
            "message" => $message
        ];

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

        if (!empty($existingUser) || $emailNew == $currentUser->getEmail() ||
            empty($emailNew) || strlen($emailNew) > 200) {
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

        $evaluation = $this->calculateMissionReliability($tracks['tracks'], $points['points'], $type);

        $mission = new Mission();
        $mission->setDrones($tracks['drones']);
        $mission->setCheckpoints($points['checkpoints']);
        $mission->setType($type);
        $mission->setEvaluation($evaluation);
        $mission->save();

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
            $points[$i][1] = 1 - $points[$i][0];
            $sum = $points[$i][0] + $points[$i][1];

            if (round($sum) != 1 || $points[$i][1] < 0) {
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
                    $sum += (int)$tracks[$j][$i];
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
                $sum += (int)$tracks[$i][$j];
            }

            if ($sum <= 0) {
                $invalidRows[] = $i;
            }
        }

        return ['tracks' => $tracks, 'invalidRows' => $invalidRows, 'invalidColumns' => $invalidColumns, 'drones' => $drones];
    }

    public function calculateMissionReliability($tracks, $points, $type): float
    {
        if ($type === 'S') {
            $reliability = $this->calculateSeriesMissionReliability($tracks, $points);
        } elseif ($type === 'P') {
            $reliability = $this->calculateParallelMissionReliability($tracks, $points);
        } else {
            $reliability = 0.0;
        }

        return number_format($reliability * 100, 2);
    }

    private function calculateSeriesMissionReliability($tracks, $points): float
    {
        $R = 1.0;
        $commonPoints = array_fill(0, count($points), 0);

        foreach ($tracks as $track) {
            foreach ($track as $index => $value) {
                if ($value == 1) {
                    $commonPoints[$index]++;
                }
            }
        }

        foreach ($tracks as $track) {
            foreach ($track as $index => $value) {
                if ($value == 1) {
                    if ($commonPoints[$index] == 1) {
                        $R *= (float)$points[$index][1]; // Probability of great data
                    } else {
                        $R *= (1.0 - (float)pow($points[$index][0], $commonPoints[$index])); // 1 - probability of only fault data
                    }
                }
            }
        }

        return $R;
    }

    private function calculateParallelMissionReliability($tracks, $points): float
    {
        $R = 1.0;
        $F = 1.0;

        foreach ($tracks as $track) {
            $success = 1.0;
            foreach ($track as $index => $value) {
                if ($value == 1) {
                    $success *= (float)$points[$index][1]; // Probability of great data
                }
            }
            $F *= (1 - $success);
        }

        return $R - $F;
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

    private function getMissions(): array {
        $formData = $this->app->getRequest();
        $min = $formData->getValue('min-evaluation-field') ?? '';
        $max = $formData->getValue('max-evaluation-field') ?? '';
        $type = $formData->getValue('mission-type-field') ?? '';
        $sql = "`type` LIKE ?";
        $parameters = ["%$type%"];

        if (!empty($min)) {
            $sql .= " AND `evaluation` > ?";
            $parameters[] = "$min";
        }

        if (!empty($max) && $max >= $min) {
            $sql .= " AND `evaluation` < ?";
            $parameters[] = "$max";
        }

        $missions = Mission::getAll($sql, $parameters);
        $data = [];

        if (count($missions) > 0) {
            for ($i = 0; $i < count($missions); $i++) {
                $data[$i]['id'] = $missions[$i]->getId();
                $data[$i]['drones'] = $missions[$i]->getDrones();
                $data[$i]['checkpoints'] = $missions[$i]->getCheckpoints();
                $data[$i]['type'] = $missions[$i]->getType();
                $data[$i]['evaluation'] = $missions[$i]->getEvaluation();
            }
        }

        return $data;
    }
}