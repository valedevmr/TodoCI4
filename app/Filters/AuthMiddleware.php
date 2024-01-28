<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

        $response = service('response');
        $authHeader = $request->getHeader('Authorization');
        $token = null;
        if (!$authHeader) {
            $response->setJSON(["success" => false, "message" => "Usuario invalido, sin autorización"]);
            $response->setStatusCode(401);
            return $response;
        }
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }

        $key_jwt = getenv('KEY_SECRET_JWT');
        try {

            $decoded = JWT::decode($token, new Key($key_jwt, 'HS256'));
        } catch (\Exception $ex) {

            $response->setJSON(["success" => false, "message" => "Acceso denegado, Token invalido"]);
            $response->setStatusCode(401);
            return $response;
        }

        $datajson = $request->getBody();
        $datos = json_decode($datajson, true);
        $datos["usuario"] = $decoded->usuario;
        $datajson = json_encode($datos);
        $request->setBody($datajson);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
