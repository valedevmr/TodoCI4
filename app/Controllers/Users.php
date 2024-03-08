<?php

namespace App\Controllers;

use App\Models\CambiosCotrasenas;
use App\Models\Users as ModelsUsers;
use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    private $nombrevalido = '/^[a-zA-Z0-9]+$/';
    private $correoValido ='/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/';

    public function index()
    {
        // $usuarios = model(ModelsUsers::class)->select('*')
        //     ->where('id', 1)
        //     ->get()->getResult();
    }



    public function create()
    {

        $data = $this->request->getJSON();


        if (!isset($data->correo)) {
            return $this->respond(["success" => false, "message" => "El correo es requerido"], 400);
        }

        if (!$data->correo) {
            return $this->respond(["success" => false, "message" => "El correo es requerido"], 400);
        }
        
        if (!preg_match($this->correoValido, $data->correo)) {
            return $this->respond(["success" => false, "message" => "Correo invalido, solo debe tener numeros, letras, signo($) o _"], 409);
        }

        try {
            $usuarios = model(ModelsUsers::class)->select('*')
                ->where('correo', $data->correo)
                ->where('eliminado', 0)
                ->countAllResults();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrrio un problema, intenta mas tarde FCU-GCU-TC"], 409);
        }


        if ($usuarios > 0) {
            return $this->respond(["success" => false, "message" => "El correo ya existe"], 409);
        }



        if (!isset($data->password)) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) es requerido"], 400);
        }
        if (!$data->password) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) es requerido"], 400);
        }

        if (strlen($data->password) < 8) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) debe tener como minimo 8 caracteres"], 400);
        }

        if (strlen($data->password) > 15) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) debe tener como maximo 15 caracteres"], 400);
        }
        if (!preg_match("/^[^=.%]+$/", $data->password)) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) no debe tener singos especiales"], 400);
        }




        if ($data->nombre_usuario) {

            if (!filter_var($data->nombre_usuario, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $this->nombrevalido)))) {
                return $this->respond(["success" => false, "message" => "El nombre solo debe tener numero, letras y espacion en blanco"], 409);
            }
        }


        try {

            $nombre = !$data->nombre_usuario ? $data->nombre_usuario : "";
            $usuario = new ModelsUsers();
            $usuario->save([
                'nombre' => $nombre,
                'correo' => $data->correo,
                'password' => password_hash($data->password, PASSWORD_DEFAULT)

            ]);
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FCU-CU.TC"], 409);
        }

        return $this->respond(["success" => true, "message" => "Usuario creado con exito"], 200);
    }




    public function updatePassword()
    {

        $data = $this->request->getJSON();


        if (!isset($data->password)) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) es requerido"], 400);
        }
        if (!$data->password) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) es requerido"], 400);
        }

        if (strlen($data->password) < 8) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) debe tener como minimo 8 caracteres"], 400);
        }

        if (strlen($data->password) > 15) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) debe tener como maximo 15 caracteres"], 400);
        }
        if (!preg_match("/^[^=.%]+$/", $data->password)) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) no debe tener singos especiales"], 400);
        }

        try {
            $usuario = model(ModelsUsers::class)->select('*')
                ->where('id', $data->id_usuario)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrrio un problema, intenta mas tarde FCU-GCU-TC"], 409);
        }

        if (!$usuario) {
            return $this->respond(["success" => false, "message" => "Esta cuenta es invalida"], 409);
        }


        if (!password_verify($data->password, $usuario["password"])) {
            return $this->respond(["success" => false, "message" => "El passwod actual es incorrecto"], 401);
        }




        if (!isset($data->newpassword)) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) es requerido"], 400);
        }
        if (!$data->newpassword) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) es requerido"], 400);
        }

        if (strlen($data->newpassword) < 8) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) debe tener como minimo 8 caracteres"], 400);
        }

        if (strlen($data->newpassword) > 15) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) debe tener como maximo 15 caracteres"], 400);
        }
        if (!preg_match("/^[^=.%]+$/", $data->newpassword)) {
            return $this->respond(["success" => false, "message" => "El password(contraseña) no debe tener singos especiales"], 400);
        }


        try {
            model(ModelsUsers::class)->where('id', $data->id_usuario)->set([
                'password' => password_hash($data->newpassword, PASSWORD_DEFAULT),
                'fecha_actualizacion' => date("d-m-Y H:i:s")
            ])->update();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FUP-UP-TC"], 409);
        }


        return $this->respond(["success" => TRUE, "message" => "Usuario creado con exito"], 200);
    }



    public function passChangeReq()
    {

        $data = $this->request->getJSON();


        if (!isset($data->correo)) {
            return $this->respond(["success" => false, "message" => "El correo es requerido"], 400);
        }
        if (!$data->correo) {
            return $this->respond(["success" => false, "message" => "El correo es requerido"], 400);
        }
        if (!filter_var($data->correo, FILTER_VALIDATE_EMAIL)) {
            return $this->respond(["success" => false, "message" => "El correo es requerido"], 422);
        }



        try {
            $usuario = model(ModelsUsers::class)->select('*')
                ->where('correo', $data->correo)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrrio un problema, intenta mas tarde FCPO-GU-TC"], 409);
        }
        $user = $usuario["id"];

        if (!$usuario) {
            return $this->respond(["success" => false, "message" => "El correo es incorrecto, no existe"], 404);
        }

        $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 16;
        $token = substr(str_shuffle($caracteres_permitidos), 0, $longitud);


        $fechaSolicitud = date("d-m-Y H:i:s");
        try {
            $correoExist = model(CambiosCotrasenas::class)->select('*')
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrrio un problema, intenta mas tarde FCPO-GU-TC"], 409);
        }

        if (!$correoExist) {
            $usuario = new CambiosCotrasenas();
            $usuario->save([
                'token' => $token,
                'correo' => $data->correo,
                'caducidad' => strtotime($fechaSolicitud)
            ]);
        } else {
            model(CambiosCotrasenas::class)->where('correo', $data->correo)->set([
                'token' => $token,
                'caducidad' => strtotime($fechaSolicitud),
                'valido' => 1,

            ])->update();
        }



        $email = \Config\Services::email();


        $email->setFrom('vale_m_r_montero@outlook.com', 'Valente');
        $email->setTo($data->correo);
        $email->setSubject('Cambio de Contraseña');
        $email->setMessage('<!DOCTYPE html>
        <html lang="en">

        <body>
            <h1>
                <a href="http://localhost:5173/sendcp?token=' . $token . '&user=' . $user . '">Cambio de contraseña</a>
            </h1>
        </body>');

        if ($email->send()) {
            return $this->respond(["success" => true, "message" => "Se ha enviado un correo a la direción que ingresaste"], 200);
        } else {
            return $this->respond(["success" => false, "message" => "Ocurrio un problema al intentar enviar la solicitud de cambio de contraseña, intenta más tarde."], 409);
        }
    }


    public function changePassOutSesion(int $id_user)
    {
        $data = $this->request->getJSON();


        if (!isset($data->token)) {
            return $this->respond(["success" => false, "message" => "El token es requerido"], 400);
        }

        if (!$data->token) {
            return $this->respond(["success" => false, "message" => "El token es requerido"], 400);
        }


        try {
            $usuario = model(ModelsUsers::class)
                ->select('*')
                ->where('id', $id_user)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrio un problema al procesar tu solicitud, intenta mas tarde o contacta a sistemas."], 409);
        }

        if (!$usuario) {
            return $this->respond(["success" => false, "message" => "Datos de usuario no encontrados"], 404);
        }

        try {
            $correoExist = model(CambiosCotrasenas::class)
                ->select('*')
                ->where("correo", $usuario["correo"])
                ->where("valido", 1)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrrio un problema, intenta mas tarde FCPOS-CC-TC"], 409);
        }
        if (!$correoExist) {
            return $this->respond(["success" => false, "message" => "El token ha expirado"], 409);
        }
        if ($correoExist->token != $data->token) {
            return $this->respond(["success" => false, "message" => "Tu peticion es invalida, el token es invalido"], 409);
        }

        $fechaactual = strtotime(date("Y-m-d H:i:s"));

        $fechacaducidad = $correoExist->caducidad + (60 * 30);
        if ($fechacaducidad < $fechaactual) {
            return $this->respond(["success" => false, "message" => "No se ha podido cambiar tu contraseña, debido a que a expirado el token"], 401);
        }



        try {
            model(CambiosCotrasenas::class)->where('correo', $usuario["correo"])->set([

                'valido' => 0,

            ])->update();
        } catch (\Throwable $th) {
            //throw $th;
        }

        try {
            $usuario = model(ModelsUsers::class)
                ->where('correo', $usuario["correo"])->set([

                    'password' => password_hash($data->new_password, PASSWORD_DEFAULT),

                ])->update();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->respond(["success" => true, "message" => "Password actualizado con exito"], 200);
    }
}
