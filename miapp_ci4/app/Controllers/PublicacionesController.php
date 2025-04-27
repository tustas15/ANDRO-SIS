<?php

namespace App\Controllers;

use App\Models\PublicacionModel;
use App\Models\ComentarioModel;
use App\Models\MeGustaModel;
use App\Models\UsuarioModel;        // Nuevo
use App\Models\ProyectoModel;       // Nuevo
use App\Models\CategoriaModel;      // Nuevo

class PublicacionesController extends BaseController
{
    protected $publicacionModel;
    protected $comentarioModel;
    protected $megustaModel;
    protected $usuarioModel;        // Nuevo
    protected $proyectoModel;       // Nuevo
    protected $categoriaModel;      // Nuevo

    public function __construct()
    {
        $this->publicacionModel = new PublicacionModel();
        $this->comentarioModel = new ComentarioModel();
        $this->megustaModel = new MeGustaModel();
        $this->usuarioModel = new UsuarioModel();      // Nuevo
        $this->proyectoModel = new ProyectoModel();     // Nuevo
        $this->categoriaModel = new CategoriaModel();   // Nuevo
    }

    public function index()
    {
        // Obtener datos para el panel de sugerencias
        $sugerencias = [
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'categorias' => $this->categoriaModel->findAll()
        ];
        // Realizamos la consulta paginada
        $publicaciones = $this->publicacionModel
            ->select('publicacion.*, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil, proyecto.titulo as proyecto_titulo')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->orderBy('publicacion.fecha_publicacion', 'DESC')
            ->paginate(10); // Se muestran 10 registros por página

        // Obtenemos el objeto pager asignado por paginate()
        $pager = $this->publicacionModel->pager;

        // Enriquecemos cada publicación con la información adicional
        foreach ($publicaciones as &$publicacion) {
            $publicacion['total_likes'] = $this->megustaModel
                ->where('id_publicacion', $publicacion['id_publicacion'])
                ->countAllResults();
            $publicacion['total_comentarios'] = $this->comentarioModel
                ->where('id_publicacion', $publicacion['id_publicacion'])
                ->countAllResults();
            $publicacion['comentarios'] = $this->comentarioModel->getCommentsWithUsers($publicacion['id_publicacion']);
            $publicacion['user_like'] = $this->megustaModel
                ->where('id_publicacion', $publicacion['id_publicacion'])
                ->where('id_usuario', session('id_usuario'))
                ->countAllResults() > 0;
        }

        $data = [
            'publicaciones' => $publicaciones,
            'pager' => $pager,
            'title' => 'Newsfeed Principal',
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'categorias' => $this->categoriaModel->findAll()
        ];

        return view('newsfeed', $data);
    }

    public function crear()
    {
        header('Content-Type: application/json');

        // Verificar autenticación
        if (!session('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Debe iniciar sesión para publicar']);
        }

        // Validar permisos de usuario
        if (!in_array(session('perfil'), ['admin', 'contratista'])) {
            return $this->response->setJSON(['error' => 'No tienes permisos para publicar']);
        }

        // Configurar reglas de validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_proyecto' => 'required|is_not_unique[proyecto.id_proyectos]',
            'titulo' => 'required|max_length[255]',
            'contenido' => 'required',
            'peso' => 'required|decimal|less_than_equal_to[100]'
        ]);

        // Ejecutar validación
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['error' => $validation->getErrors()]);
        }

        // Obtener datos del POST
        $idProyecto = $this->request->getPost('id_proyecto');
        $peso = (float)$this->request->getPost('peso');

        // Verificar propiedad del proyecto
        $proyecto = $this->proyectoModel->find($idProyecto);
        if ($proyecto['id_contratista'] != session('id_usuario')) {
            return $this->response->setJSON(['error' => 'No tienes permisos para este proyecto']);
        }

        // Validar suma de pesos
        $totalPeso = $this->publicacionModel
            ->where('id_proyectos', $idProyecto)
            ->selectSum('peso')
            ->get()
            ->getRow()->peso ?? 0;

        if (($totalPeso + $peso) > 100) {
            return $this->response->setJSON([
                'error' => ['peso' => 'La suma de porcentajes no puede superar el 100% (Actual: ' . $totalPeso . '%)']
            ]);
        }

        // Procesar imagen
        $nombreImagen = null;
        $imagen = $this->request->getFile('imagen');

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            // Crear directorio si no existe
            $uploadDir = FCPATH . 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generar nombre único
            $nombreImagen = $imagen->getRandomName();
            $imagen->move($uploadDir, $nombreImagen);
        }

        try {
            // Crear registro
            $this->publicacionModel->save([
                'id_proyectos' => $idProyecto,
                'titulo' => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('contenido'),
                'imagen' => $nombreImagen,
                'peso' => $peso
            ]);

            // Actualizar estado del proyecto si alcanza 100%
            if (($totalPeso + $peso) == 100) {
                $this->proyectoModel->update($idProyecto, ['etapa' => 'finalizado']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Publicación creada exitosamente',
                'totalPeso' => $totalPeso + $peso
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al crear publicación: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error interno al guardar la publicación'
            ]);
        }
    }

    public function verPublicacion($id_publicacion)
    {
        $publicacion = $this->publicacionModel
            ->select('publicacion.*, proyecto.titulo as proyecto_titulo, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->find($id_publicacion);
        if (!$publicacion) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $comentarios = $this->comentarioModel
            ->select('comentarios.*, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil')
            ->join('usuarios', 'usuarios.id_usuario = comentarios.id_usuario')
            ->where('id_publicacion', $id_publicacion)
            ->orderBy('fecha', 'ASC')
            ->findAll();



        $data = [
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas(),
            'publicacion' => $publicacion,
            'comentarios' => $comentarios,
            'total_likes' => $this->megustaModel->obtenerTotalMegustasPublicacion($id_publicacion),
            'user_like' => $this->megustaModel->usuarioDioMegustaPublicacion(session('id_usuario'), $id_publicacion),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'categorias' => $this->categoriaModel->findAll()

        ];

        return view('feed', $data);
    }

    public function crearComentario()
    {
        $this->comentarioModel->save([
            'id_usuario' => session('id_usuario'),
            'id_publicacion' => $this->request->getPost('id_publicacion'),
            'comentario' => $this->request->getPost('comentario')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function toggleLike()
    {
        if (!$this->request->isAJAX() || !session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'No autorizado']);
        }

        try {
            $idPublicacion = $this->request->getPost('id_publicacion');
            $idUsuario = session('id_usuario');

            $accion = $this->megustaModel->toggleLike($idUsuario, $idPublicacion);

            return $this->response->setJSON([
                'success' => true,
                'total_likes' => $this->megustaModel->obtenerTotalMegustasPublicacion($idPublicacion),
                'user_like' => ($accion === 'like')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en toggleLike: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error interno']);
        }
    }

    public function view($id_proyecto)
    {
        // Obtener el proyecto
        $proyecto = $this->proyectoModel->getProyectoConContratista($id_proyecto);

        // Obtener publicaciones relacionadas
        $publicaciones = $this->publicacionModel->getPublicacionesConEstadisticas($id_proyecto);

        $data = [
            
            'categorias' => $this->categoriaModel->findAll(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'proyecto' => $proyecto,
            'publicaciones' => $publicaciones,
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas()
        ];

        return view('seleccion_proyectos', $data);
    }

    public function vista()
    {
        // Obtener proyectos con su información básica
        $proyectos = $this->proyectoModel
            ->select('proyecto.*, categorias.nombre as categoria_nombre, COALESCE(SUM(publicacion.peso), 0) as total_peso')
            ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
            ->join('categorias', 'categorias.id_categoria = proyecto.id_categoria')
            ->where('proyecto.id_contratista', session('id_usuario'))
            ->groupBy('proyecto.id_proyectos')
            ->having('total_peso < 100')
            ->orderBy('proyecto.fecha_publicacion', 'DESC')
            ->findAll();

        // Obtener IDs de proyectos para buscar sus publicaciones
        $proyectoIds = array_column($proyectos, 'id_proyectos');

        // Obtener todas las publicaciones relacionadas
        $publicaciones = $this->publicacionModel
            ->whereIn('id_proyectos', $proyectoIds)
            ->orderBy('fecha_publicacion', 'DESC')
            ->findAll();

        // Organizar publicaciones por proyecto
        $publicacionesPorProyecto = [];
        foreach ($publicaciones as $pub) {
            $publicacionesPorProyecto[$pub['id_proyectos']][] = $pub;
        }

        // Asignar publicaciones a cada proyecto
        foreach ($proyectos as &$proyecto) {
            $proyecto['publicaciones'] = $publicacionesPorProyecto[$proyecto['id_proyectos']] ?? [];
        }

        $data = [
            'proyectos' => $proyectos,
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas(),
            'categorias' => $this->categoriaModel->getCategoriasConProyectos()
        ];

        return view('crear_publicacion', $data);
    }
}
