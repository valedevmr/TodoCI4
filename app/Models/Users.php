<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{

    protected $table      = 'usuario';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';



    protected $allowedFields = ['nombre', 'correo', 'password', 'fecha_creacion', 'eliminado'];

   
}
