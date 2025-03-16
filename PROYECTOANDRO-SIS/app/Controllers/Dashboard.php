<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        // Verificar sesión
        if (!$this->session->has('user_id')) {
            return redirect()->to('auth');
        }

        // Cargar modelos
        $categoriaModel = new \App\Models\CategoriaModel();
        $proyectoModel = new \App\Models\ProyectoModel();

        $data = [
            'user' => [
                'id' => $this->session->get('user_id'),
                'nombre' => $this->session->get('nombre'),
                'apellido' => $this->session->get('apellido'),
                'perfil' => $this->session->get('perfil'),
                'imagen_perfil' => $this->session->get('imagen_perfil')
            ]
        ];

        // Load appropriate dashboard based on user profile
        switch ($this->session->get('perfil')) {
            case 'admin':
                return $this->adminDashboard($data);
            case 'contratista':
                return $this->contratistaDashboard($data);
            case 'publico':
                return $this->publicoDashboard($data);
            default:
                $this->session->destroy();
                return redirect()->to('auth')->with('error', 'Perfil no válido');
        }
    }

    private function adminDashboard($data)
    {
        $proyectoModel = new \App\Models\ProyectoModel();
        $usuarioModel = new \App\Models\UsuarioModel();
        $contratistas = $usuarioModel->getContratistasConEstadisticas();
    

        // Get stats for admin dashboard
        $data['totalProyectos'] = $proyectoModel->countAll();
        $data['proyectosPorEtapa'] = $proyectoModel->countByStage();
        $data['totalContratistas'] = $usuarioModel->where('perfil', 'contratista')->countAllResults();
        $data['totalUsuarios'] = $usuarioModel->where('perfil', 'publico')->countAllResults();

        // Get recent conversations
        $conversacionModel = new \App\Models\ConversacionModel();
        $data['conversaciones'] = $conversacionModel->getRecentWithContratistas(5);

        return view('dashboard/admin', $data);
    }

    private function contratistaDashboard($data)
    {
        $proyectoModel = new \App\Models\ProyectoModel();

        // Get contratista's projects
        $data['proyectos'] = $proyectoModel->where('id_contratista', $this->session->get('user_id'))->findAll();
        $data['proyectosPorEtapa'] = $proyectoModel->countByStageForContratista($this->session->get('user_id'));
        $data['totalPresupuesto'] = $proyectoModel->getTotalBudget($this->session->get('user_id'));

        // Get recent publications
        $publicacionModel = new \App\Models\PublicacionModel();
        $data['publicaciones'] = $publicacionModel->getRecentByContratista($this->session->get('user_id'), 5);

        // Get conversations with admin
        $conversacionModel = new \App\Models\ConversacionModel();
        $data['conversaciones'] = $conversacionModel->getForContratista($this->session->get('user_id'));

        return view('dashboard/contratista', $data);
    }

    private function publicoDashboard($data)
    {
        $proyectoModel = new \App\Models\ProyectoModel();
        $publicacionModel = new \App\Models\PublicacionModel();

        // Get recent projects and publications
        $data['proyectos'] = $proyectoModel->getRecent(5);
        $data['publicaciones'] = $publicacionModel->getRecent(5);

        // Get user likes and comments
        $data['misLikes'] = $publicacionModel->getUserLikes($this->session->get('user_id'));
        $data['misComentarios'] = $publicacionModel->getUserComments($this->session->get('user_id'));

        return view('dashboard/publico', $data);
    }
}
