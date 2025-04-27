<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\ProyectoModel;
use App\Models\MeGustaModel;
use App\Models\ComentarioModel;
use App\Models\UsuarioModel;

class UsuariosController extends BaseController
{

    protected $categoriaModel;
    protected $proyectoModel;
    protected $usuarioModel;
    protected $megustaModel;
    protected $comentarioModel;
    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
        $this->proyectoModel = new ProyectoModel();
        $this->usuarioModel = new UsuarioModel();
        $this->megustaModel = new MeGustaModel();
        $this->comentarioModel = new ComentarioModel();
    }

    public function formCrearUsuario()
    {
        $data = [
            'title' => 'Crear Nuevo Usuario',
            'perfiles' => ['admin', 'contratista', 'publico'],
            'estados' => ['activo', 'desactivo'],
            'categorias' => $this->categoriaModel->getCategoriasConProyectos(),
            'proyectos' => $this->proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll(5),
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas()
        ];

        return view('admin/crear', $data);
    }

    public function guardarUsuario()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|max_length[100]',
            'apellido' => 'required|max_length[100]',
            'correo' => [
                'rules' => 'required|valid_email|is_unique[usuarios.correo]',
                'errors' => [
                    'is_unique' => 'Este correo ya está registrado.'
                ]
            ],
            'contrasena' => 'required|min_length[6]',
            'perfil' => 'required|in_list[admin,contratista,publico]',
            'estado' => 'required|in_list[activo,desactivo]',
            'imagen_perfil' => 'max_size[imagen_perfil,1024]|is_image[imagen_perfil]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $imagen = $this->request->getFile('imagen_perfil');
        $nombreImagen = 'default.jpg'; // Nombre por defecto

        if ($imagen->isValid() && !$imagen->hasMoved()) {
            // Crear directorio si no existe
            $directorioDestino = ROOTPATH . 'public/images/usuarios';
            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0755, true);
            }

            $nombreImagen = $imagen->getRandomName();
            $imagen->move($directorioDestino, $nombreImagen); // Guardar en /usuarios
        }

        $this->usuarioModel->save([
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo'),
            'contrasena' => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'perfil' => $this->request->getPost('perfil'),
            'estado' => $this->request->getPost('estado'),
            'imagen_perfil' => $nombreImagen
        ]);


        return redirect()->to('admin/usuarios/crear')->with('success', 'Usuario creado exitosamente');
    }

    public function indexAdmins()
    {
        return $this->cargarVistaPorRol('admin', 'Administradores');
    }

    public function indexContratistas()
    {
        return $this->cargarVistaPorRol('contratista', 'Contratistas');
    }

    public function indexCiudadanos()
    {
        return $this->cargarVistaPorRol('publico', 'Ciudadanos');
    }

    private function cargarVistaPorRol($rol, $titulo)
    {
        $model = new UsuarioModel();

        $data = [
            'title' => 'Gestión de ' . $titulo,
            'usuarios' => $model->getUsersByRole($rol),
            'rolActual' => $rol,
            'pager' => $model->pager,
            'contratistas' => $this->usuarioModel->getContratistasConProyectos(),
            'activosCount' => $model->countActiveUsersByRole($rol)
        ];

        return view("admin/usuarios_{$rol}s", $data);
    }

    public function updateStatus()
    {
        if (session('perfil') !== 'admin') {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $model = new UsuarioModel();
        $id = $this->request->getPost('id');
        $currentUserId = session('id_usuario'); // ID del usuario logueado

        // Prevenir auto-desactivación
        if ($id == $currentUserId) {
            return $this->response->setJSON([
                'error' => 'No puedes desactivar tu propio perfil'
            ]);
        }

        $user = $model->find($id);
        $newStatus = $user['estado'] === 'activo' ? 'desactivo' : 'activo';
        $model->update($id, ['estado' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'newStatus' => $newStatus,
            'buttonClass' => $newStatus === 'activo' ? 'delete-friend' : 'add-friend',
            'buttonText' => $newStatus === 'activo' ? 'Desactivar' : 'Activar'
        ]);
    }

    public function perfil()
    {
        $usuario = $this->usuarioModel->find(session('id_usuario'));

        $data = [
            'title' => 'Configuración de Perfil',
            'usuario' => $usuario,
            'validation' => \Config\Services::validation()
        ];

        return view('auth/personal_information', $data);
    }

    public function actualizarPerfil()
    {
        $usuario = $this->usuarioModel->find(session('id_usuario'));

        $reglas = [
            'nombre' => 'required|max_length[100]',
            'apellido' => 'required|max_length[100]',
            'correo' => [
                'rules' => "required|valid_email|is_unique[usuarios.correo,id_usuario,{$usuario['id_usuario']}]",
                'errors' => [
                    'is_unique' => 'Este correo ya está registrado.'
                ]
            ],
            'imagen_perfil' => 'max_size[imagen_perfil,1024]|is_image[imagen_perfil]'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datosActualizados = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'correo' => $this->request->getPost('correo')
        ];

        // Manejo de la imagen
        $imagen = $this->request->getFile('imagen_perfil');
        if ($imagen->isValid() && !$imagen->hasMoved()) {
            $directorio = ROOTPATH . 'public/images/usuarios';

            // Eliminar imagen anterior si no es la predeterminada
            if ($usuario['imagen_perfil'] != 'default.jpg') {
                @unlink($directorio . '/' . $usuario['imagen_perfil']);
            }

            $nombreImagen = $imagen->getRandomName();
            $imagen->move($directorio, $nombreImagen);
            $datosActualizados['imagen_perfil'] = $nombreImagen;
        }

        $this->usuarioModel->update($usuario['id_usuario'], $datosActualizados);

        // Actualizar datos en sesión
        session()->set([
            'nombre' => $datosActualizados['nombre'],
            'apellido' => $datosActualizados['apellido'],
            'imagen_perfil' => $datosActualizados['imagen_perfil'] ?? $usuario['imagen_perfil']
        ]);

        return redirect()->to('auth/perfil')->with('success', 'Perfil actualizado correctamente');
    }

    public function view($id_usuario){
        // Obtener contratista
        $contratista = $this->usuarioModel->find($id_usuario);
        
        // Verificar si es contratista
        if(!$contratista || $contratista['perfil'] !== 'contratista'){
            return redirect()->back()->with('error', 'Contratista no encontrado');
        }
    
        // Obtener proyectos y publicaciones
        $proyectoModel = new ProyectoModel();
        $publicacionModel = new \App\Models\PublicacionModel();
    
        $proyectos = $proyectoModel->where('id_contratista', $id_usuario)->findAll();
        
        // Agregar publicaciones a cada proyecto
        foreach($proyectos as &$proyecto){
            $proyecto['publicaciones'] = $publicacionModel->where('id_proyectos', $proyecto['id_proyectos'])->findAll();
        
        // Agregar este cálculo de porcentaje
        $proyecto['porcentaje_total'] = 0;
        foreach($proyecto['publicaciones'] as $pub){
            $proyecto['porcentaje_total'] += $pub['peso'];
        }
        $proyecto['porcentaje_total'] = min($proyecto['porcentaje_total'], 100); // Limitar a 100%


        // Agregar estadísticas a cada publicación
        foreach($proyecto['publicaciones'] as &$publicacion){
            $publicacion['total_likes'] = $this->megustaModel->where('id_publicacion', $publicacion['id_publicacion'])->countAllResults();
            $publicacion['total_comentarios'] = $this->comentarioModel->where('id_publicacion', $publicacion['id_publicacion'])->countAllResults();
        }
        }
    

        $data = [
            'title' => 'Perfil de '.$contratista['nombre'],
            'contratista' => $contratista,
            'proyectos' => $proyectos,
            'categorias' => $this->categoriaModel->findAll(),
            'contratistas' => $this->usuarioModel->getContratistasConEstadisticas()
        ];
    
        return view('seleccion_contratista', $data);
    }
    public function countActiveUsersByRole($rol)
    {
        return $this->where('perfil', $rol)
            ->where('estado', 'activo')
            ->countAllResults();
    }
}
