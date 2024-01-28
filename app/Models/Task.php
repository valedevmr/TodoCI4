<?php

namespace App\Models;

use CodeIgniter\Model;

class Task extends Model
{

    protected $table      = 'task';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';



    protected $allowedFields = [
        'titulo',
        'descripcion',
        'eliminado',
        'estado',
        'tipo',
        'fecha_creacion',
        'fecha_actualizacion',
        'id_usuario',
    ];
}
