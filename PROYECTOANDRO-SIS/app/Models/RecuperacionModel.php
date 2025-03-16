<?php

namespace App\Models;

use CodeIgniter\Model;

class RecuperacionModel extends Model
{
    protected $table = 'recuperacioncontrasenas';
    protected $primaryKey = 'id_recuperacion';
    protected $allowedFields = [
        'id_usuario', 'token', 'fecha_solicitud', 'usado'
    ];
    protected $useTimestamps = false;
}