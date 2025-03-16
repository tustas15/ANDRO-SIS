<?php
namespace App\Models;

use CodeIgniter\Model;

class ConversacionModel extends Model
{
    protected $table = 'conversaciones';
    protected $primaryKey = 'id_conversacion';
    protected $allowedFields = [
        'id_admin', 'id_contratista', 'fecha_inicio'
    ];
    protected $useTimestamps = false;
    
    public function getRecentWithContratistas($limit = 5)
    {
        return $this->select('conversaciones.*, admin.nombre as admin_nombre, admin.apellido as admin_apellido, contratista.nombre as contratista_nombre, contratista.apellido as contratista_apellido, mensajes.mensaje as ultimo_mensaje, mensajes.fecha as fecha_ultimo_mensaje')
                    ->join('usuarios as admin', 'admin.id_usuario = conversaciones.id_admin')
                    ->join('usuarios as contratista', 'contratista.id_usuario = conversaciones.id_contratista')
                    ->join('mensajes', 'mensajes.id_conversacion = conversaciones.id_conversacion', 'left')
                    ->orderBy('mensajes.fecha', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    public function getForContratista($id_contratista)
    {
        return $this->select('conversaciones.*, admin.nombre as admin_nombre, admin.apellido as admin_apellido, mensajes.mensaje as ultimo_mensaje, mensajes.fecha as fecha_ultimo_mensaje')
                    ->join('usuarios as admin', 'admin.id_usuario = conversaciones.id_admin')
                    ->join('mensajes', 'mensajes.id_conversacion = conversaciones.id_conversacion', 'left')
                    ->where('conversaciones.id_contratista', $id_contratista)
                    ->orderBy('mensajes.fecha', 'DESC')
                    ->findAll();
    }
}