<?php

namespace App\Implementation;

use App\Interfaces\AuthI;
use CodeIgniter\HTTP\RequestInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authentication implements AuthI
{
    protected $usuario =  null;

    public function authUser($data): array
    {
        if (!isset($data->correo)) {
            return ["success" => false, "code" => 409];
        }
        if (!$data->correo) {
            return ["success" => false, "code" => 409];
        }


        try {
            $userModel = new \App\Models\Users();
            $this->usuario = $userModel->where('correo', $data->correo)->first();
        } catch (\Throwable $th) {
            return ["success" => false, "message" => "Ocurrio un problema, vdu-TC", "code" => 409];
        }

        if (!$this->usuario) {
            return ["success" => false, "message" => "El correo no existe", "code" => 404];
        }
        if (!isset($data->password)) {
            return ["success" => false, "code" => 409];
        }
        if (!$data->password) {
            return ["success" => false, "code" => 409];
        }
        if (!password_verify($data->password, $this->usuario["password"])) {
            return ["success" => false, "message" => "Contraseña invalida", "code" => 401];
        }


        return ["success" => true];
    }

    public function gToken(): array
    {

        try {
            $key_jwt = getenv('KEY_SECRET_JWT');

            $payload = [
                'nombre' => $this->usuario["nombre"],
                'usuario' => $this->usuario["id"],
                'iat' => time(),
                'exp' => time() + 60 * 60,
            ];

            $jwt = JWT::encode($payload, $key_jwt, 'HS256');
        } catch (\Throwable $th) {
            return ["success" => false, "message" => "Ocurrio un problema, GTK-TC", "code" => 409];
        }

        return ["success" => true, "jwt" => $jwt];
    }

    public function vToken(RequestInterface $request)
    {
        $response = service('response');

        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader) {
            return [["success" => false, "message" => "Usuario invalido, sin autorización"], "code" => 401];
        }
        $token =null;
         if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }

        $key_jwt = getenv('KEY_SECRET_JWT');
        
        try {

            $decoded = JWT::decode($token, new Key($key_jwt, 'HS256'));
        } catch (\Exception $ex) {

            return [["success" => false, "message" => "Usuario invalido, sin autorizaciónd"],["code"=>401]];
            
        }

        return [["success" => true],["decoded"=>$decoded]];
        
    }
}
