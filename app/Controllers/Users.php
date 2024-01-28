<?php

namespace App\Controllers;

use App\Models\Users as ModelsUsers;
use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    private $nombrevalido = '/^[a-zA-Z0-9]+$/';
    private $correoValido = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/';

    public function index(): string
    {

        $usuarios = model(ModelsUsers::class)->select('*')
            ->where('id', 1)
            ->get()->getResult();


        // $usuarios = $usuarios->findAll();


        echo json_encode($usuarios);
        exit;
        // return "esto debe funcionar";
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

            if (!filter_var($data->nombre_usuario, FILTER_VALIDATE_REGEXP, ["regexp" => $this->nombrevalido])) {
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

        return $this->respond(["success" => false, "message" => "Usuario creado con exito"], 200);
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
            $usuarios = model(ModelsUsers::class)->select('*')
                ->where('id', 4)
                ->get()->getResult();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrrio un problema, intenta mas tarde FCU-GCU-TC"], 409);
        }

        if (count($usuarios) < 1) {
            return $this->respond(["success" => false, "message" => "Esta cuenta es invalida"], 409);
        }

        echo json_encode($usuarios);
        exit;



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



        return $this->respond(["success" => false, "message" => "Usuario creado con exito"], 200);
    }
}
