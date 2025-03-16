<?php 
namespace App\Models;

use CodeIgniter\Model;

class ProyectoModel extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyectos';
    protected $allowedFields = [
        'id_contratista', 'titulo', 'fecha_publicacion', 'etapa', 'id_categoria', 'presupuesto'
    ];
    protected $useTimestamps = false;
    
    public function countByStage()
    {
        $result = $this->select('etapa, COUNT(*) as total')
                       ->groupBy('etapa')
                       ->findAll();
        
        $stages = ['planificacion' => 0, 'ejecucion' => 0, 'finalizado' => 0];
        
        foreach ($result as $row) {
            $stages[$row['etapa']] = $row['total'];
        }
        
        return $stages;
    }
    
    public function countByStageForContratista($id_contratista)
    {
        $result = $this->select('etapa, COUNT(*) as total')
                       ->where('id_contratista', $id_contratista)
                       ->groupBy('etapa')
                       ->findAll();
        
        $stages = ['planificacion' => 0, 'ejecucion' => 0, 'finalizado' => 0];
        
        foreach ($result as $row) {
            $stages[$row['etapa']] = $row['total'];
        }
        
        return $stages;
    }
    
    public function getTotalBudget($id_contratista)
    {
        $result = $this->selectSum('presupuesto')
                       ->where('id_contratista', $id_contratista)
                       ->first();
        
        return $result['presupuesto'] ?? 0;
    }
    
    public function getRecent($limit = 5)
    {
        return $this->select('proyecto.*, categorias.nombre as categoria, usuarios.nombre as contratista_nombre, usuarios.apellido as contratista_apellido')
                    ->join('categorias', 'categorias.id_categoria = proyecto.id_categoria')
                    ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
                    ->orderBy('fecha_publicacion', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    public function getWithDetails($id_proyecto)
    {
        return $this->select('proyecto.*, categorias.nombre as categoria, usuarios.nombre as contratista_nombre, usuarios.apellido as contratista_apellido')
                    ->join('categorias', 'categorias.id_categoria = proyecto.id_categoria')
                    ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
                    ->where('id_proyectos', $id_proyecto)
                    ->first();
    }
}