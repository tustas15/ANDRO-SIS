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
        'imagen_perfil'
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
}
