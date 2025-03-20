<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CategoriaModel;
use App\Models\ProyectoModel;
use App\Models\PublicacionModel;
use App\Models\UsuarioModel;

class AdminController extends Controller {
    public function dashboard() {
        if (!session()->get('isLoggedIn') || session()->get('perfil') !== 'admin') {
            return redirect()->to('auth');
        }

        // Cargar modelos
        $categoriaModel = new CategoriaModel();
        $proyectoModel = new ProyectoModel();
        $publicacionModel = new PublicacionModel();
        $usuarioModel = new UsuarioModel();

        // Datos para secciones
        $data['categorias'] = $categoriaModel->findAll();
        $data['proyectos'] = $proyectoModel->orderBy('fecha_publicacion', 'DESC')->findAll();
        $data['contratistas'] = $usuarioModel->getContratistasConEstadisticas();
        
        // Paginación de publicaciones
        $data['publicaciones'] = $publicacionModel
            ->select('publicacion.*, proyecto.titulo as proyecto_titulo, usuarios.nombre, usuarios.apellido, usuarios.imagen_perfil')
            ->join('proyecto', 'proyecto.id_proyectos = publicacion.id_proyectos')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->orderBy('publicacion.fecha_publicacion', 'DESC')
            ->paginate(5);
            
        $data['pager'] = $publicacionModel->pager;

        return view('dashboard/admin', $data);
    }
}