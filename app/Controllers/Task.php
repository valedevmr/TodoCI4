<?php

namespace App\Controllers;

use App\Models\Task as ModelsTask;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\RESTful\ResourceController;


class Task extends ResourceController
{
    private $nombrevalido = '/^[a-zA-Z0-9]+$/';
    private $correoValido = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/';
    private $estatus = ["creado", "en proceso", "pendiente", "completado"];
    private $prioridadTipo = ["normal", "regular", "urgente"];
    private $user;


    public function create()
    {

        $data = $this->request->getJSON();

        if (!isset($data->titulo)) {
            return $this->respond(["success" => false, "message" => "El título es requerido"], 400);
        }
        if (!$data->titulo) {
            return $this->respond(["success" => false, "message" => "El título es requerido"], 400);
        }


        if (!isset($data->descripcion)) {
            return $this->respond(["success" => false, "message" => "La descripción es requerida"], 400);
        }
        if (!$data->descripcion) {
            return $this->respond(["success" => false, "message" => "La descripción es requerida"], 400);
        }
        $tipo = !$data->tipo ? 'normal' : $data->tipo;
        $descripcion = TRIM($data->descripcion);

        try {


            $task = new ModelsTask();
            $task->save([
                'titulo' => $data->titulo,
                'descripcion' => $descripcion,
                'tipo' => $data->tipo,
                'id_usuario' => $data->id_usuario,
            ]);
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FCT-IT-TC"], 409);
        }



        return $this->respond(["success" => true, "message" => "Tarea creada con exito"], 201);
    }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




    public function index()
    {
        $data = $this->request->getJSON();

        $usuario = [];
        try {
            $usuarios = model(ModelsTask::class)->select('*')
                ->where('id_usuario', $data->id_usuario)
                ->where('eliminado', 0)
                ->get()->getResult();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FIT-GT-TC"], 409);
        }

        return $this->respond(["success" => true, "message" => "Datos encontrados", "datos" => $usuarios], 200);
    }




    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    public function show($id_task = null)
    {
        $data = $this->request->getJSON();

        try {

            $usuario = model(ModelsTask::class)->select('titulo,descripcion')
                ->where('id_usuario', $data->id_usuario)
                ->where('eliminado', 0)
                ->where('id', $id_task)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FIT-GT-TC"], 409);
        }

        if (!$usuario) {
            return $this->respond(["success" => false, "message" => "La tarea no existe"], 404);
        }
        return $this->respond(["success" => true, "message" => "Tarea encontrada", "task" => $usuario], 200);
    }



    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    public function update($id_task = null)
    {

        $data = $this->request->getJSON();

        if (!$id_task) {
            return $this->respond(["success" => false, "message" => "El ID es requerida"], 400);
        }
        if (!is_numeric($id_task)) {
            return $this->respond(["success" => false, "message" => "El ID de la tarea debe ser númerico"], 400);
        }


        if (!isset($data->titulo)) {
            return $this->respond(["success" => false, "message" => "El título es requerido"], 400);
        }
        if (!$data->titulo) {
            return $this->respond(["success" => false, "message" => "El título es requerido"], 400);
        }


        if (!isset($data->descripcion)) {
            return $this->respond(["success" => false, "message" => "La descripción es requerida"], 400);
        }
        if (!$data->descripcion) {
            return $this->respond(["success" => false, "message" => "La descripción es requerida"], 400);
        }


        if (!isset($data->tipo)) {
            return $this->respond(["success" => false, "message" => "El tipo de prioridad es requerida"], 400);
        }
        if (!$data->tipo) {
            return $this->respond(["success" => false, "message" => "El tipo de prioridad es requerida"], 400);
        }
        if (!in_array($data->tipo, $this->prioridadTipo)) {
            return $this->respond(["success" => false, "message" => "El tipo de prioridad es requerida"], 400);
        }


        if (!isset($data->estado)) {
            return $this->respond(["success" => false, "message" => "El estado es requerida"], 400);
        }
        if (!$data->estado) {
            return $this->respond(["success" => false, "message" => "El estado es requerida"], 400);
        }
        if (!in_array($data->estado, $this->estatus)) {
            return $this->respond(["success" => false, "message" => "El estado es requerida"], 400);
        }


        try {

            $usuario = model(ModelsTask::class)->select('titulo')
                ->where('id_usuario', $data->id_usuario)
                ->where('eliminado', 0)
                ->where('id', $id_task)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FIT-GT-TC"], 409);
        }

        if (!$usuario) {
            return $this->respond(["success" => false, "message" => "La tarea no existe"], 404);
        }


        try {
            model(ModelsTask::class)->where('id', $id_task)->set([
                'titulo' => $data->titulo,
                'descripcion' => $data->descripcion,
                'tipo' => $data->tipo,
                'estado' => $data->estado,
                'fecha_actualizacion' => date("d-m-Y H:i:s")
            ])->update();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FIT-GT-TC"], 409);
        }

        return $this->respond(["success" => true, "message" => "Tarea: '" . $data->titulo . " ID " . $id_task . "' actualizada con exito"], 200);
    }



    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




    public function delete($id_task = null)
    {

        $data = $this->request->getJSON();

        if (!$id_task) {
            return $this->respond(["success" => false, "message" => "El ID es requerida"], 400);
        }
        if (!is_numeric($id_task)) {
            return $this->respond(["success" => false, "message" => "El ID de la tarea debe ser númerico"], 400);
        }


        try {

            $usuario = model(ModelsTask::class)->select('titulo')
                ->where('id_usuario', $data->id_usuario)
                ->where('eliminado', 0)
                ->where('id', $id_task)
                ->first();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FIT-GT-TC"], 409);
        }

        if (!$usuario) {
            return $this->respond(["success" => false, "message" => "La tarea no existe"], 404);
        }



        try {
            model(ModelsTask::class)
                ->where('id', $id_task)
                ->where('id_usuario', $data->id_usuario)
                ->set([
                    'eliminado' => 1
                ])->update();
        } catch (\Throwable $th) {
            return $this->respond(["success" => false, "message" => "Ocurrió un problema, intenta más tarde FIT-GT-TC"], 409);
        }

        return $this->respond(["success" => true, "message" => "Tarea eliminada con exito"], 200);
    }
}
