<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyectoModel extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyectos';
    protected $allowedFields = [
        'id_contratista',
        'titulo',
        'fecha_publicacion',
        'fecha_fin',
        'etapa',
        'id_categoria',
        'presupuesto'
    ];
    protected $useTimestamps = false;

    protected $validationRules = [
        'titulo' => 'required|max_length[255]',
        'id_categoria' => 'required|integer',
        'id_contratista' => 'required|integer',
        'presupuesto' => 'required|decimal',
        'etapa' => 'required|in_list[planificacion,ejecucion,finalizado]'
    ];

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
            ->orderBy('fecha_publicacion', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    // En ProyectoModel.php
    public function getWithDetails($id_proyecto)
    {
        return $this->select('proyecto.*, 
        COUNT(publicacion.id_publicacion) as publicaciones,
        COUNT(comentarios.id_comentario) as comentarios')
            ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
            ->join('comentarios', 'comentarios.id_publicacion = publicacion.id_publicacion', 'left')
            ->where('proyecto.id_proyectos', $id_proyecto)
            ->groupBy('proyecto.id_proyectos')
            ->first();
    }
    public function getProyectosRecientes($limit = 5)
    {
        return $this->orderBy('fecha_publicacion', 'ASC')
            ->findAll($limit);
    }
    public function detalle($id)
    {
        $model = new ProyectoModel();
        $data['proyecto'] = $model->find($id);

        return view('detalle_proyecto', $data);
    }


    public function getProyectosPorCategoria()
    {
        return $this->select('categorias.nombre, proyecto.*, 
            COUNT(publicacion.id_publicacion) as publicaciones,
            COUNT(comentarios.id_comentario) as comentarios,
            usuarios.nombre as nombre_contratista, 
            usuarios.apellido as apellido_contratista,')
            ->join('categorias', 'categorias.id_categoria = proyecto.id_categoria')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
            ->join('comentarios', 'comentarios.id_publicacion = publicacion.id_publicacion', 'left')
            ->groupBy('proyecto.id_proyectos, categorias.id_categoria')
            ->findAll();
    }

    public function getProyectoConContratista($id_proyecto)
    {
        return $this->select('proyecto.*, usuarios.nombre as contratista_nombre, usuarios.apellido as contratista_apellido')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->where('proyecto.id_proyectos', $id_proyecto)
            ->first();
    }
    public function getProyectosConAvance($idContratista)
    {
        return $this->select('proyecto.*, COALESCE(SUM(publicacion.peso), 0) as total_peso')
            ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
            ->where('proyecto.id_contratista', $idContratista)
            ->groupBy('proyecto.id_proyectos')
            ->findAll();
    }
    public function getProyectosConPublicaciones($idProyecto = null)
    {
        $builder = $this->select('proyecto.*, 
        GROUP_CONCAT(publicacion.titulo SEPARATOR "|") as publicaciones,
        COUNT(publicacion.id_publicacion) as total_publicaciones,
        SUM(publicacion.peso) as avance')
            ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
            ->groupBy('proyecto.id_proyectos');

        if ($idProyecto) {
            $builder->where('proyecto.id_proyectos', $idProyecto);
            return $builder->first();
        }

        return $builder->findAll();
    }
}
