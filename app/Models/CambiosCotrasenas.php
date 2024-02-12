<?php

namespace App\Models;

use CodeIgniter\Model;

class CambiosCotrasenas extends Model
{

    protected $table      = 'cambio_password';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'object';



    protected $allowedFields = [
        'correo',
        'token',
        'caducidad',
        'valido',
        'conteo',
    ];
}