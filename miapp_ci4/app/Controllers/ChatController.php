<?php

namespace App\Controllers;

use App\Models\ArchivoAdjuntoModel;
use App\Models\ConversacionModel;
use App\Models\MensajeModel;
use App\Models\UsuarioModel;

class ChatController extends BaseController
{
    public function index()
    {
        // Verificar sesión
        if (!session()->has('id_usuario')) {
            return redirect()->to('/login');
        }

        $usuarioModel = new UsuarioModel();
        $currentUser = session()->get('id_usuario');
        $perfil = session()->get('perfil');

        // Obtener contactos según rol
        if ($perfil === 'admin') {
            $contactos = $usuarioModel->whereIn('perfil', ['admin', 'contratista'])->findAll();
        } else {
            $contactos = $usuarioModel->where('perfil', 'admin')->findAll();
        }

        return view('chat', [
            'contactos' => $contactos
        ]);
    }

    public function conversacion($idContacto)
    {
        $conversacionModel = new ConversacionModel();
        $mensajeModel = new MensajeModel();
        $currentUser = session()->get('id_usuario');
        $perfil = session()->get('perfil');

        // Determinar roles para la conversación
        if ($perfil === 'admin') {
            $conversacion = $conversacionModel->where('id_admin', $currentUser)
                ->where('id_contratista', $idContacto)
                ->first();
        } else {
            $conversacion = $conversacionModel->where('id_admin', $idContacto)
                ->where('id_contratista', $currentUser)
                ->first();
        }

        // Crear nueva conversación si no existe
        if (!$conversacion) {
            $dataConversacion = [
                'id_admin' => ($perfil === 'admin') ? $currentUser : $idContacto,
                'id_contratista' => ($perfil === 'contratista') ? $currentUser : $idContacto
            ];

            $conversacionModel->insert($dataConversacion);
            $idConversacion = $conversacionModel->getInsertID();
        } else {
            $idConversacion = $conversacion['id_conversacion'];
        }

        // Obtener mensajes
        $mensajes = $mensajeModel->where('id_conversacion', $idConversacion)
            ->orderBy('fecha', 'ASC')
            ->findAll();

        // Obtener adjuntos para cada mensaje
        $archivoModel = new ArchivoAdjuntoModel();
        foreach ($mensajes as &$mensaje) {
            $mensaje['adjuntos'] = $archivoModel->where('id_mensaje', $mensaje['id_mensaje'])->findAll();
        }

        $usuarioModel = new UsuarioModel();
        $contacto = $usuarioModel->find($idContacto);


        return view('conversacion', [
            'mensajes' => $mensajes,
            'idConversacion' => $idConversacion,
            'idContacto' => $idContacto,
            'contacto' => $contacto
        ]);
    }

    public function enviarMensaje()
    {
        $mensajeModel = new MensajeModel();
        $archivoModel = new ArchivoAdjuntoModel();

        $validation = \Config\Services::validation();

        // Validación estricta
        $validation->setRules([
            'mensaje' => 'required_without[archivo]',
            'archivo' => [
                'max_size[archivo,2048]',
                'mime_in[archivo,image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        $data = [
            'id_conversacion' => $this->request->getPost('id_conversacion'),
            'id_remitente' => session()->get('id_usuario'),
            'mensaje' => $this->request->getPost('mensaje')
        ];

        // Insertar mensaje primero
        $mensajeId = $mensajeModel->insert($data);

        // Manejar archivo
        $archivo = $this->request->getFile('archivo');
        if ($archivo && $archivo->isValid()) {
            $mimeType = $archivo->getClientMimeType();
            log_message('debug', 'MIME Type detectado: ' . $mimeType); // <--- Añadir esto

            $nuevoNombre = $archivo->getRandomName();
            if (!$archivo->hasMoved()) {
                $archivo->move(WRITEPATH . 'uploads', $nuevoNombre);
            }
            $dataArchivo = [
                'id_mensaje' => $mensajeId,
                'ruta_archivo' => $nuevoNombre,
                'tipo' => $this->determinarTipoArchivo($archivo->getClientMimeType())
            ];

            $archivoModel->insert($dataArchivo);
        }

        return redirect()->back();
    }

    // Nuevo método para descargas
    public function descargar($idArchivo)
    {
        $archivoModel = new ArchivoAdjuntoModel();
        $archivo = $archivoModel->find($idArchivo);

        if (!$archivo) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = WRITEPATH . 'uploads/' . $archivo['ruta_archivo'];

        return $this->response->download($filePath, null);
    }

    private function determinarTipoArchivo($mime)
    {
        $mime = strtolower($mime);

        $documentos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream' // Agregar si es necesario para .doc
        ];

        if (strpos($mime, 'image/') !== false) {
            return 'imagen';
        } elseif (in_array($mime, $documentos)) {
            return 'documento';
        } else {
            return 'otro';
        }
    }
}
