<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\UsuarioModel;
use App\Models\ProyectoModel;
use App\Models\PublicacionModel;

class ProyectosController extends BaseController
{
    protected $categoriaModel;
    protected $usuarioModel;
    protected $proyectoModel;
    protected $publicacionModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
        $this->usuarioModel = new UsuarioModel();
        $this->proyectoModel = new ProyectoModel();
        $this->publicacionModel = new PublicacionModel();
    }

    // Método para mostrar la vista
    public function index()
    {
        $proyectos = $this->proyectoModel->getProyectosPorCategoria();

        // Agrupar por categoría
        $proyectosPorCategoria = [];
        foreach ($proyectos as $proyecto) {
            $catId = $proyecto['id_categoria'];
            if (!isset($proyectosPorCategoria[$catId])) {
                $proyectosPorCategoria[$catId] = [
                    'nombre' => $proyecto['nombre'],
                    'proyectos' => []
                ];
            }
            $proyectosPorCategoria[$catId]['proyectos'][] = $proyecto;
        }

        $data = [
            'proyectosPorCategoria' => $proyectosPorCategoria,
            'categorias' => $this->categoriaModel->getCategoriasConProyectos(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'contratistas' => $this->usuarioModel->where('perfil', 'contratista')->findAll()
        ];

        return view('proyectos', $data);
    }

    // Método para crear
    public function crear()
    {
        if ($this->validate($this->proyectoModel->validationRules)) {
            $this->proyectoModel->save([
                'titulo' => $this->request->getPost('titulo'),
                'id_categoria' => $this->request->getPost('id_categoria'),
                'id_contratista' => $this->request->getPost('id_contratista'),
                'presupuesto' => $this->request->getPost('presupuesto'),
                'etapa' => $this->request->getPost('etapa'),
                'fecha_publicacion' => date('Y-m-d H:i:s') // Añadir fecha automática
            ]);
            return redirect()->back()->with('success', 'Proyecto creado');
        }
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Método para actualizar
    public function update()
    {
        $data = $this->request->getPost();

        if ($this->proyectoModel->save($data)) {
            return redirect()->back()->with('success', 'Proyecto actualizado correctamente');
        }

        return redirect()->back()->with('error', 'Error al actualizar el proyecto');
    }

    // Método para eliminar
    public function eliminar()
    {
        $id = $this->request->getPost('id_proyectos');

        if (!$id) {
            return redirect()->back()->with('error', 'ID no proporcionado');
        }

        // Obtener proyecto con relaciones
        $proyecto = $this->proyectoModel->getWithDetails($id);

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        // Verificar existencia de publicaciones/comentarios
        if ($proyecto['publicaciones'] > 0 || $proyecto['comentarios'] > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: tiene contenido relacionado');
        }

        // Eliminar
        if ($this->proyectoModel->delete($id)) {
            return redirect()->back()->with('success', 'Proyecto eliminado');
        }

        return redirect()->back()->with('error', 'Error al eliminar');
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
