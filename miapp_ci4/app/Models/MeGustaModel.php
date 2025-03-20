<?php

namespace App\Models;

use CodeIgniter\Model;

class MeGustaModel extends Model
{
    protected $table = 'megusta';
    protected $primaryKey = 'id_megusta';
    protected $allowedFields = [
        'id_usuario', 'id_publicacion', 'fecha'
    ];
    protected $useTimestamps = false;
    
    public function checkUserLiked($id_usuario, $id_publicacion)
    {
        return $this->where('id_usuario', $id_usuario)
                    ->where('id_publicacion', $id_publicacion)
                    ->countAllResults() > 0;
    }
}