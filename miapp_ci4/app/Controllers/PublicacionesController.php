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
            // Cambiar esto:
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'categorias' => $this->categoriaModel->findAll()
        ];

        return view('newsfeed', $data);
    }

    public function crear()
    {
        // Agrega este header para respuestas JSON
        header('Content-Type: application/json');
        log_message('debug', 'Iniciando método crear()');
        log_message('debug', 'Datos POST: ' . print_r($this->request->getPost(), true));
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_proyecto' => 'required|is_not_unique[proyecto.id_proyectos]',
            'titulo' => 'required|max_length[255]',
            'contenido' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'error' => $validation->getErrors()
            ]);
        }
        $proyecto = $this->proyectoModel->find($this->request->getPost('id_proyecto'));
        if ($proyecto['id_contratista'] != session('id_usuario')) {
            return $this->response->setJSON(['error' => 'No tienes permisos para este proyecto']);
        }
        if (!in_array(session('perfil'), ['admin', 'contratista'])) {
            return $this->response->setJSON(['error' => 'No tienes permisos para publicar']);
        }

        $imagen = $this->request->getFile('imagen');
        $nombreImagen = null;

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $nombreImagen = $imagen->getRandomName();
            $imagen->move(WRITEPATH . 'uploads', $nombreImagen);
        }

        $this->publicacionModel->save([
            'id_proyectos' => $this->request->getPost('id_proyecto'),
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('contenido'),
            'imagen' => $nombreImagen
        ]);

        $data = [
            'id_proyectos' => $this->request->getPost('id_proyecto'),
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('contenido'),
            'imagen' => $nombreImagen
        ];

        if ($this->publicacionModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Publicación creada exitosamente'
            ]);
        }

        return $this->response->setJSON([
            'error' => 'Error al guardar la publicación'
        ]);
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
        $idPublicacion = $this->request->getPost('id_publicacion');
        $idUsuario = session('id_usuario');

        if (
            $this->megustaModel->where('id_usuario', $idUsuario)
            ->where('id_publicacion', $idPublicacion)
            ->countAllResults() > 0
        ) {
            // Eliminar like
            $this->megustaModel->where('id_usuario', $idUsuario)
                ->where('id_publicacion', $idPublicacion)
                ->delete();
            $accion = 'removed';
        } else {
            // Agregar like
            $this->megustaModel->save([
                'id_usuario' => $idUsuario,
                'id_publicacion' => $idPublicacion
            ]);
            $accion = 'added';
        }

        return $this->response->setJSON([
            'success' => true,
            'total' => $this->megustaModel->where('id_publicacion', $idPublicacion)->countAllResults()
        ]);
    }
}
