<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\UsuarioModel;
use App\Models\ProyectoModel;

class ProyectosController extends BaseController
{
    protected $categoriaModel;
    protected $usuarioModel;
    protected $proyectoModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->proyectoModel = new ProyectoModel();
    }

    public function index()
    {
        $data = [
            'categorias' => [],
            'contratistas' => [],
            'proyectos' => []
        ];

        try {
            // Procesar creación de categoría
            if ($this->request->getMethod() === 'post' && $this->request->getPost('crear_categoria')) {
                $this->_procesarCreacionCategoria();
            }

            // Procesar eliminación de categoría
            if ($this->request->getMethod() === 'post' && $this->request->getPost('eliminar_categoria')) {
                $this->_procesarEliminacionCategoria();
            }

            // Obtener datos para el dashboard
            $data['categorias'] = $this->categoriaModel->getCategoriasConProyectos();
            $data['contratistas'] = $this->usuarioModel->getContratistasConEstadisticas();
            $data['proyectos'] = $this->proyectoModel->getProyectosRecientes(5);
        } catch (\Exception $e) {
            log_message('error', 'Error en Proyectos controller: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar datos del dashboard');
        }

        return view('dashboard/admin', $data);
    }

    private function _procesarCreacionCategoria()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre_categoria' => 'required|max_length[100]'
        ]);

        if ($validation->withRequest($this->request)->run()) {
            $this->categoriaModel->save([
                'nombre' => $this->request->getPost('nombre_categoria')
            ]);
            session()->setFlashdata('mensaje', 'Categoría creada exitosamente');
        } else {
            session()->setFlashdata('errors', $validation->getErrors());
        }
    }

    private function _procesarEliminacionCategoria()
    {
        $id = $this->request->getPost('id_categoria');

        if ($this->categoriaModel->tieneProyectos($id)) {
            session()->setFlashdata('error', 'No se puede eliminar: tiene proyectos asociados');
            return;
        }

        if ($this->categoriaModel->delete($id)) {
            session()->setFlashdata('mensaje', 'Categoría eliminada');
        } else {
            session()->setFlashdata('error', 'Error al eliminar categoría');
        }
        
        log_message('info', "Intentando eliminar categoría ID: $id");
    }
}
