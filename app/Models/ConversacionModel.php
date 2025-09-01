<?php namespace App\Models;

use CodeIgniter\Model;

class ConversacionModel extends Model
{
    protected $table = 'conversaciones';
    protected $primaryKey = 'id_conversacion';
    protected $allowedFields = ['id_admin', 'id_contratista'];
}