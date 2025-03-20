<?php

namespace App\Models;

use CodeIgniter\Model;

class ComentarioModel extends Model
{
    protected $table = 'comentarios';
    protected $primaryKey = 'id_comentario';
    protected $allowedFields = [
        'id_usuario',
        'id_publicacion',
        'comentario',
        'fecha'
    ];
    protected $useTimestamps = false;

    public function getCommentsWithUsers($idPublicacion)
    {
        return $this->select('comentarios.*, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil')
            ->join('usuarios', 'usuarios.id_usuario = comentarios.id_usuario')
            ->where('id_publicacion', $idPublicacion)
            ->orderBy('fecha', 'ASC')
            ->findAll();
    }
}
