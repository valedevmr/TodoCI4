<?php

namespace App\Controllers;

use App\Implementation\Authentication;
use CodeIgniter\RESTful\ResourceController;

use Firebase\JWT\JWT;


class Login extends ResourceController
{
    private $auth = null;

    public function __construct()
    {
        $this->auth  = new Authentication();
    }

    public function auth()
    {
        $data = $this->request->getJSON();
        $response = $this->auth->authUser($data);

        if (!$response["success"])
            return $this->respond(["success" => false, "message" => $response["message"]], $response["code"]);

        $response = $this->auth->gToken();
        if (!$response["success"]) 
            return $this->respond(["success" => false, "message" => $response["message"]], $response["code"]);

        return $this->respond(["success" => true, "message" => "Usuario valido", "token" => $response["jwt"]], 200);
    }
}
