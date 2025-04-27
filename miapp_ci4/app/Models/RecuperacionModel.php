<?php

namespace App\Models;

use CodeIgniter\Model;

class RecuperacionModel extends Model
{
    protected $table = 'recuperacioncontrasenas';
    protected $primaryKey = 'id_recuperacion';
    protected $allowedFields = [
        'id_usuario',
        'token',
        'fecha_solicitud',
        'usado'
    ];
    protected $useTimestamps = false; // Desactivar timestamps automáticos

    protected $dateFormat = 'datetime'; // Formato de fecha compatible con MySQL

    public function borrarTokensAnteriores($idUsuario)
    {
        return $this->where('id_usuario', $idUsuario)->delete();
    }

    public function tokenValido($token)
    {
        return $this->where('token', $token)
            ->where('usado', 0)
            ->where('fecha_solicitud >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->first();
    }

    public function marcarComoUsado($idRecuperacion)
    {
        return $this->update($idRecuperacion, ['usado' => 1]);
    }
}
