<?php

namespace App\Models;

use CodeIgniter\Model;

class PublicacionModel extends Model
{
    protected $table = 'publicacion';
    protected $primaryKey = 'id_publicacion';
    protected $allowedFields = [
        'id_proyectos',
        'titulo',
        'descripcion',
        'imagen',
        'fecha_publicacion',
        'peso'
    ];
    protected $useTimestamps = false;

    public function getRecent($limit = 5)
    {
        return $this->select('publicacion.*, proyecto.titulo as proyecto_titulo, COUNT(megusta.id_megusta) as total_likes, COUNT(comentarios.id_comentario) as total_comentarios')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('megusta', 'megusta.id_publicacion = publicacion.id_publicacion', 'left')
            ->join('comentarios', 'comentarios.id_publicacion = publicacion.id_publicacion', 'left')
            ->groupBy('publicacion.id_publicacion')
            ->orderBy('fecha_publicacion', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getRecentByContratista($id_contratista, $limit = 5)
    {
        return $this->select('publicacion.*, proyecto.titulo as proyecto_titulo, COUNT(megusta.id_megusta) as total_likes, COUNT(comentarios.id_comentario) as total_comentarios')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('megusta', 'megusta.id_publicacion = publicacion.id_publicacion', 'left')
            ->join('comentarios', 'comentarios.id_publicacion = publicacion.id_publicacion', 'left')
            ->where('proyecto.id_contratista', $id_contratista)
            ->groupBy('publicacion.id_publicacion')
            ->orderBy('fecha_publicacion', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getUserLikes($id_usuario)
    {
        return $this->select('publicacion.*, proyecto.titulo as proyecto_titulo')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('megusta', 'megusta.id_publicacion = publicacion.id_publicacion')
            ->where('megusta.id_usuario', $id_usuario)
            ->orderBy('megusta.fecha', 'DESC')
            ->findAll();
    }

    public function getUserComments($id_usuario)
    {
        return $this->select('publicacion.*, proyecto.titulo as proyecto_titulo, comentarios.comentario, comentarios.fecha')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('comentarios', 'comentarios.id_publicacion = publicacion.id_publicacion')
            ->where('comentarios.id_usuario', $id_usuario)
            ->orderBy('comentarios.fecha', 'DESC')
            ->findAll();
    }

    public function getNewsfeed()
    {
        return $this->select('publicacion.*, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil, proyecto.titulo as proyecto_titulo')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->orderBy('publicacion.fecha_publicacion', 'DESC')
            ->findAll();
    }
}
