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
           COUNT(p.id_proyectos) AS total_proyectos
    FROM categorias c
    LEFT JOIN proyecto p ON c.id_categoria = p.id_categoria
    GROUP BY c.id_categoria
    ORDER BY c.nombre ASC
");

            
            return $this->select('categorias.*, COUNT(proyecto.id_proyectos) as total_proyectos')
                ->join('proyecto', 'proyecto.id_categoria = categorias.id_categoria', 'left')
                ->groupBy('categorias.id_categoria')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error en getCategoriasConProyectos: ' . $e->getMessage());
            return [];
        }
    }

    public function tieneProyectos($id_categoria)
    {
        return $this->db->table('proyecto')
            ->where('id_categoria', $id_categoria)
            ->countAllResults() > 0;
    }
    public function detalle($id)
    {
        $model = new CategoriaModel();
        $data['categoria'] = $model->find($id);
        
        return view('detalle_categoria', $data);
    }
}
