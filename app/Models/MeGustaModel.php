<?php

namespace App\Models;

use CodeIgniter\Model;

class MeGustaModel extends Model
{
    protected $table = 'megusta';
    protected $primaryKey = 'id_megusta';
    protected $allowedFields = ['id_usuario', 'id_publicacion', 'fecha'];
    protected $useTimestamps = false;
    
    public function toggleLike($idUsuario, $idPublicacion)
    {
        $like = $this->where([
            'id_usuario' => $idUsuario,
            'id_publicacion' => $idPublicacion
        ])->first();

        if ($like) {
            $this->delete($like[$this->primaryKey]);
            return 'unlike';
        }
        
        $this->insert([
            'id_usuario' => $idUsuario,
            'id_publicacion' => $idPublicacion,
            'fecha' => date('Y-m-d H:i:s')
        ]);
        return 'like';
    }

    public function obtenerTotalMegustasPublicacion($idPublicacion)
    {
        return $this->where('id_publicacion', $idPublicacion)
                   ->countAllResults();
    }

    public function usuarioDioMegustaPublicacion($idUsuario, $idPublicacion)
    {
        if (empty($idUsuario)) return false;
        
        return $this->where('id_usuario', $idUsuario)
                   ->where('id_publicacion', $idPublicacion)
                   ->countAllResults() > 0;
    }

    // Método deprecado (mantener por compatibilidad temporal)
    public function checkUserLiked($idUsuario, $idPublicacion)
    {
        return $this->usuarioDioMegustaPublicacion($idUsuario, $idPublicacion);
    }
}