<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;


class Login extends ResourceController
{
    public function auth()
    {

        $data = $this->request->getJSON();

        if (!isset($data->correo)) {
            return $this->respond(["success" => false], 409);
        }
        if (!$data->correo) {
            return $this->respond(["success" => false], 409);
        }
        $response = [];

        $user = null;
        try {
            $userModel = new \App\Models\Users();
            $user = $userModel->where('correo', $data->correo)->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrio un problema, GTK-TC"], 409);
        }

        if (!$user) {
            return $this->respond(["success" => false, "message" => "El correo no existe"], 404);
        }
        if (!isset($data->password)) {
            return $this->respond(["success" => false], 409);
        }
        if (!$data->password) {
            return $this->respond(["success" => false], 409);
        }
        if (!password_verify($data->password, $user["password"])) {
            return $this->respond(["success" => false, "message" => "Contraseña invalida"], 401);
        }



        try {
            $key_jwt = getenv('KEY_SECRET_JWT');

            $payload = [
                'nombre' => $user["nombre"],
                'usuario' => $user["id"],
                'iat' => time(),
                'exp' => time() + 60 * 60,
            ];

            $jwt = JWT::encode($payload, $key_jwt, 'HS256');
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrio un problema, GTK-TC"], 409);
        }

        return $this->respond(["success" => true, "message" => "Usuario valido", "token" => $jwt], 200);
    }
}
