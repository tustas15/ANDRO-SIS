<?php namespace App\Models;

use CodeIgniter\Model;

class MensajeModel extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'id_mensaje';
    protected $allowedFields = ['id_conversacion', 'id_remitente', 'mensaje'];
}