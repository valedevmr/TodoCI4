<?php

namespace App\Filters;

use App\Implementation\Authentication;
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
        $auth = new Authentication();
        $authR = $auth->vToken($request);

        if (!$authR[0]["success"]) {
            $response->setJSON($authR[0])->setStatusCode($authR[1]["code"]);
            return $response;
        }
      
        try {
            $datajson = $request->getBody();
            $datos = json_decode($datajson, true);
            $datos["id_usuario"] = $authR[1]["decoded"]->usuario;
            $datajson = json_encode($datos);
            $request->setBody($datajson);
        } catch (\Throwable $th) {
            $response->setJSON(["success" => false, "message" => "Ocurri un problema, Contacta con el departamento de sistemas"]);
            $response->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
