<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = ['nombre'];

    // En CategoriaModel.php
    public function getCategoriasConProyectos()
    {
        try {
            $query = $this->db->query("
            SELECT c.id_categoria, 
                   c.nombre AS categoria_nombre,
                   GROUP_CONCAT(p.titulo SEPARATOR ', ') AS proyectos
            FROM categorias c
            LEFT JOIN proyecto p ON c.id_categoria = p.id_categoria
            GROUP BY c.id_categoria
            ORDER BY c.nombre ASC
        ");

            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error en getCategoriasConProyectos: ' . $e->getMessage());
            return [];
        }
    }
}
