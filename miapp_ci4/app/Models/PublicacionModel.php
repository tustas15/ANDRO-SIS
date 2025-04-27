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

    public function getPublicacionesConEstadisticas($id_proyecto)
    {
        return $this->select('publicacion.*, 
            COUNT(DISTINCT megusta.id_megusta) as total_likes, 
            COUNT(DISTINCT comentarios.id_comentario) as total_comentarios')
            ->join('megusta', 'megusta.id_publicacion = publicacion.id_publicacion', 'left')
            ->join('comentarios', 'comentarios.id_publicacion = publicacion.id_publicacion', 'left')
            ->where('publicacion.id_proyectos', $id_proyecto)
            ->groupBy('publicacion.id_publicacion')
            ->orderBy('publicacion.fecha_publicacion', 'DESC')
            ->findAll();
    }

    public function getPublicacionesConMetricas()
    {
        return $this->db->query("
        SELECT 
            pub.*,
            p.titulo AS proyecto_titulo,
            u.nombre AS contratista_nombre,
            COUNT(m.id_megusta) AS total_likes,
            COUNT(c.id_comentario) AS total_comentarios,
            DATEDIFF(NOW(), pub.fecha_publicacion) AS dias_publicado
        FROM publicacion pub
        JOIN proyecto p ON pub.id_proyectos = p.id_proyectos
        JOIN usuarios u ON p.id_contratista = u.id_usuario
        LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
        LEFT JOIN comentarios c ON pub.id_publicacion = c.id_publicacion
        GROUP BY pub.id_publicacion
        ORDER BY pub.fecha_publicacion ASC
    ")->getResultArray();
    }

    public function getPublicacionMasPopular()
    {
        return $this->db->query("
        SELECT 
            pub.titulo,
            COUNT(m.id_megusta) AS total_likes
        FROM publicacion pub
        LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
        GROUP BY pub.id_publicacion
        ORDER BY total_likes DESC
        LIMIT 1
    ")->getRowArray();
    }

    public function getTotalCommentsByContractor($idContratista)
    {
        return $this->db->table('comentarios')
            ->select('COUNT(comentarios.id_comentario) as total')
            ->join('publicacion', 'publicacion.id_publicacion = comentarios.id_publicacion')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->where('proyecto.id_contratista', $idContratista)
            ->get()
            ->getRow()
            ->total ?? 0;
    }
}
