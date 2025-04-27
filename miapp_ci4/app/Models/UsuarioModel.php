<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'perfil',
        'estado',
        'codigo_verificacion', 
        'verificado', 
        'estado', 
        'imagen_perfil',
        'created_at'
    ];
    protected $useTimestamps = false;

    public function getUserByEmail($email)
    {
        return $this->where('correo', $email)->first();
    }

    public function getContratistas()
    {
        return $this->where('perfil', 'contratista')
            ->where('estado', 'activo')
            ->findAll();
    }

    public function getContratistasConEstadisticas()
    {
        $query = $this->db->query("
            SELECT 
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.imagen_perfil,
                COUNT(DISTINCT p.id_proyectos) AS total_proyectos,
                COUNT(DISTINCT pub.id_publicacion) AS total_publicaciones,
                COUNT(DISTINCT m.id_megusta) AS total_megustas
            FROM 
                usuarios u
            LEFT JOIN 
                proyecto p ON u.id_usuario = p.id_contratista
            LEFT JOIN 
                publicacion pub ON p.id_proyectos = pub.id_proyectos
            LEFT JOIN 
                megusta m ON pub.id_publicacion = m.id_publicacion
            WHERE 
                u.perfil = 'contratista' 
                AND u.estado = 'activo'
            GROUP BY 
                u.id_usuario
            ORDER BY 
                u.nombre ASC
        ");
        return $this->select('usuarios.*, COUNT(proyecto.id_proyectos) as total_proyectos')
            ->join('proyecto', 'proyecto.id_contratista = usuarios.id_usuario', 'left')
            ->where('perfil', 'contratista')
            ->groupBy('usuarios.id_usuario')
            ->findAll();

        return $query->getResultArray();
    }
    public function getContratistasDetallados()
    {
        return $this->db->query("
        SELECT 
            u.id_usuario,
            u.nombre,
            u.apellido,
            COUNT(p.id_proyectos) AS total_proyectos,
            COUNT(pub.id_publicacion) AS total_publicaciones,
            SUM(p.presupuesto) AS presupuesto_total,
            COUNT(m.id_megusta) AS total_megustas,
            COUNT(c.id_comentario) AS total_comentarios
        FROM usuarios u
        LEFT JOIN proyecto p ON u.id_usuario = p.id_contratista
        LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
        LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
        LEFT JOIN comentarios c ON pub.id_publicacion = c.id_publicacion
        WHERE u.perfil = 'contratista'
        GROUP BY u.id_usuario
        ORDER BY total_proyectos ASC
    ")->getResultArray();
    }

    public function getEstadisticasGlobalesContratistas()
    {
        return $this->db->query("
        SELECT 
            COUNT(DISTINCT u.id_usuario) AS total_contratistas,
            COUNT(p.id_proyectos) AS total_proyectos,
            SUM(p.presupuesto) AS presupuesto_total
        FROM usuarios u
        LEFT JOIN proyecto p ON u.id_usuario = p.id_contratista
        WHERE u.perfil = 'contratista'")->getRowArray();
    }


    public function getContratistasConProyectos()
    {
        return $this->select('usuarios.*, COUNT(proyecto.id_proyectos) as total_proyectos')
            ->join('proyecto', 'proyecto.id_contratista = usuarios.id_usuario', 'left')
            ->where('perfil', 'contratista')
            ->groupBy('usuarios.id_usuario')
            ->findAll();
    }

    public function getUsersByRole($rol)
    {
        $builder = $this->select('usuarios.id_usuario, usuarios.nombre, usuarios.apellido, usuarios.correo, usuarios.perfil, usuarios.estado, usuarios.imagen_perfil')
            ->orderBy('nombre', 'ASC');

        if ($rol === 'contratista') {
            $builder->select('COUNT(DISTINCT proyecto.id_proyectos) as total_proyectos, 
                         COUNT(DISTINCT publicacion.id_publicacion) as total_publicaciones')
                ->join('proyecto', 'proyecto.id_contratista = usuarios.id_usuario', 'left')
                ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
                ->groupBy('usuarios.id_usuario');
        }

        if ($rol === 'publico') {
            $builder->select('COUNT(DISTINCT comentarios.id_comentario) as total_comentarios,
                         COUNT(DISTINCT megusta.id_megusta) as total_likes')
                ->join('comentarios', 'comentarios.id_usuario = usuarios.id_usuario', 'left')
                ->join('megusta', 'megusta.id_usuario = usuarios.id_usuario', 'left')
                ->groupBy('usuarios.id_usuario');
        }

        return $builder->where('perfil', $rol)
            ->paginate(10);
    }

    public function countActiveUsersByRole($rol)
    {
        return $this->where('perfil', $rol)
            ->where('estado', 'activo')
            ->countAllResults();
    }

    public function getContratistasConEstadisticasCompletas()
    {
        return $this->db->query("
        SELECT 
            u.*,
            COUNT(DISTINCT p.id_proyectos) AS total_proyectos,
            COUNT(DISTINCT pub.id_publicacion) AS total_publicaciones,
            SUM(p.presupuesto) AS presupuesto_total,
            COUNT(DISTINCT m.id_megusta) AS total_megustas,
            COUNT(DISTINCT c.id_comentario) AS total_comentarios
        FROM usuarios u
        LEFT JOIN proyecto p ON u.id_usuario = p.id_contratista
        LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
        LEFT JOIN megusta m ON pub.id_publicacion = m.id_publicacion
        LEFT JOIN comentarios c ON pub.id_publicacion = c.id_publicacion
        WHERE u.perfil = 'contratista'
        GROUP BY u.id_usuario
    ")->getResultArray();
    }
}
