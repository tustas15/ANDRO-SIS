<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\ProyectoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CategoriasController extends BaseController
{
    protected $categoriaModel;
    protected $proyectoModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
        $this->proyectoModel = new ProyectoModel();
        $this->usuarioModel = new UsuarioModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'titulo' => 'Gestión de Categorías',
            'categorias' => $this->categoriaModel->getCategoriasConProyectos(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'contratistas' => $this->usuarioModel->getContratistasConProyectos(),
            'mensaje' => session()->getFlashdata('mensaje'),
            'error' => session()->getFlashdata('error')
        ];

        return view('categorias', $data);
    }

    public function crear()
    {
        if (!$this->request->is('post')) {
            throw new PageNotFoundException('Acción no permitida');
        }

        $nombre = trim($this->request->getPost('nombre'));
        if (empty($nombre)) {
            return redirect()->back()
                ->withInput()
                ->with('error', ['El nombre de la categoría no puede estar vacío']);
        }

        $validation = service('validation');
        $validation->setRules([
            'nombre' => [
                'rules' => 'required|max_length[255]|is_unique[categorias.nombre]',
                'errors' => [
                    'is_unique' => 'Esta categoría ya existe'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validation->getErrors());
        }

        try {
            $this->categoriaModel->save([
                'nombre' => $this->request->getPost('nombre')
            ]);
            return redirect()->to('/categorias')
                ->with('mensaje', 'Categoría creada exitosamente');
        } catch (\Exception $e) {
            log_message('error', 'Error al crear categoría: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error crítico al crear la categoría');
        }
    }

    public function eliminar()
    {
        $id = $this->request->getPost('id_categoria');

        if (!$this->validate(['id_categoria' => 'required|numeric'])) {
            return redirect()->back()
                ->with('error', 'ID de categoría inválido');
        }
        $proyectosAsociados = $this->proyectoModel
            ->where('id_categoria', $id)
            ->countAllResults();
        try {
            // Verificar proyectos usando el modelo
            if ($this->proyectoModel->where('id_categoria', $id)->countAllResults() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar categoría con proyectos asociados');
            }

            if ($this->categoriaModel->delete($id)) {
                session()->setFlashdata('mensaje', 'Categoría eliminada exitosamente');
            } else {
                session()->setFlashdata('error', 'La categoría no existe');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar categoría: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error crítico al eliminar: ' . $e->getMessage());
        }

        return redirect()->to('/categorias');
    }

    public function view($id_categoria)
    {
        // Validate the category ID
        $categoria = $this->categoriaModel->find($id_categoria);
    if (!$categoria) {
        throw new PageNotFoundException('Categoría no encontrada');
    }

    // Obtener proyectos con sus publicaciones y porcentaje
    $proyectos = $this->proyectoModel
        ->where('id_categoria', $id_categoria)
        ->findAll();

    // Cargar modelo de publicaciones
    $publicacionModel = new \App\Models\PublicacionModel();

    foreach($proyectos as &$proyecto){
        // Obtener publicaciones del proyecto
        $publicaciones = $publicacionModel->where('id_proyectos', $proyecto['id_proyectos'])->findAll();
        
        // Calcular porcentaje
        $proyecto['porcentaje_total'] = 0;
        foreach($publicaciones as $pub){
            $proyecto['porcentaje_total'] += $pub['peso'];
        }
        $proyecto['porcentaje_total'] = min($proyecto['porcentaje_total'], 100);
    }

        $data = [
            'categoria' => $categoria,
            'categorias' => $this->categoriaModel->findAll(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'proyecto' => $proyectos,
            'contratistas' => $this->usuarioModel->getContratistasConProyectos(),
            'mensaje' => session()->getFlashdata('mensaje'),
            'error' => session()->getFlashdata('error')
        ];

        return view('seleccion_categorias', $data);
    }
}
