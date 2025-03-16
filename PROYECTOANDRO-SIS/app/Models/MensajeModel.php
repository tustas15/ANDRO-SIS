<?php 
namespace App\Models;

use CodeIgniter\Model;

class MensajeModel extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'id_mensaje';
    protected $allowedFields = [
        'id_conversacion', 'id_remitente', 'mensaje', 'fecha'
    ];
    protected $useTimestamps = false;
    
    public function getConversationMessages($id_conversacion)
    {
        return $this->select('mensajes.*, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil, usuarios.perfil')
                    ->join('usuarios', 'usuarios.id_usuario = mensajes.id_remitente')
                    ->where('id_conversacion', $id_conversacion)
                    ->orderBy('fecha', 'ASC')
                    ->findAll();
    }
}