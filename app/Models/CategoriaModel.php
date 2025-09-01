<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = ['nombre'];
    protected $returnType = 'array';

    public function getCategoriasConProyectos()
    {
        // Versión mejorada con GROUP_CONCAT
        return $this->select('categorias.*, 
            COUNT(proyecto.id_proyectos) as proyectos,
            GROUP_CONCAT(proyecto.titulo SEPARATOR ", ") as titulos_proyectos')
            ->join('proyecto', 'proyecto.id_categoria = categorias.id_categoria', 'left')
            ->groupBy('categorias.id_categoria')
            ->orderBy('categorias.nombre', 'ASC')
            ->findAll();
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

    public function getCategoriaConProyectos($id_categoria)
    {
        return $this->select('categorias.*')
            ->where('id_categoria', $id_categoria)
            ->first();
    }

    public function getCategoriasConEstadisticas()
    {
        return $this->db->query("
        SELECT 
            c.*,
            COUNT(p.id_proyectos) AS total_proyectos,
            SUM(p.presupuesto) AS presupuesto_total,
            AVG(pub.peso) AS promedio_avance
        FROM categorias c
        LEFT JOIN proyecto p ON c.id_categoria = p.id_categoria
        LEFT JOIN publicacion pub ON p.id_proyectos = pub.id_proyectos
        GROUP BY c.id_categoria
    ")->getResultArray();
    }
}
