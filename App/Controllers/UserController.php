<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Models\User;

class UserController extends AControllerBase
{
    public function index(): Response
    {
        return $this->html();
    }

    public function message(): Response
    {
        return $this->html();
    }

    public function checkRegister(): Response
    {
        $formData = $this->app->getRequest();
        $name = $formData->getValue("sign-up-name");
        $email = $formData->getValue("sign-up-email");
        $password = $formData->getValue("sign-up-password");
        $message = "Failed to register!";

        if (!empty($name) && !empty($email) && !empty($password)) {
            if ($this->app->getAuth()->register($name, $email)) {
                $user = new User();
                $user->setLogin($name);
                $user->setEmail($email);
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
                $user->save();
                $message = "Successfully registered!";

                $data = ["name" => $name, "message" => $message];
                return $this->redirect($this->url("user.message", $data));
            }
        }

        $data = ["option" => 2, "name" => $name, "email" => $email, "sign-up-message" => $message];
        return $this->redirect($this->url("user.index", $data));
    }

    public function checkLogin(): Response
    {
        $formData = $this->app->getRequest();
        $email = $formData->getValue("sign-in-email");
        $password = $formData->getValue("sign-in-password");
        $message = "Failed to login!";

        if (!empty($email) && !empty($password)) {
            if ($this->app->getAuth()->login($email, $password)) {
                $message = "Successfully logged in!";

                $data = ["name" => $this->app->getAuth()->getLoggedUserName(), "message" => $message];
                return $this->redirect($this->url("user.message", $data));
            }
        }

        $data = ["option" => 1, "email" => $email, "sign-in-message" => $message];
        return $this->redirect($this->url("user.index", $data));
    }
}
