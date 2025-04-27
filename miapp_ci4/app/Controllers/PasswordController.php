<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\RecuperacionModel;
use App\Libraries\ResendService;

class PasswordController extends BaseController
{
    public function index()
    {
        // Verificar si está autenticado
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth');
        }

        $data = [
            'title' => 'Cambiar Contraseña',
            'errors' => session()->getFlashdata('errors'), // Captura errores de validación
            'error' => session()->getFlashdata('error'),   // Error general
            'success' => session()->getFlashdata('success') // Mensaje éxito
        ];

        return view('auth/change_password', $data);
    }

    public function update()
    {
        // Validación de entrada
        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => [
                'label' => 'Contraseña actual',
                'rules' => 'required',
                'errors' => [
                    'required' => 'La {field} es obligatoria'
                ]
            ],
            'new_password' => [
                'label' => 'Nueva contraseña',
                'rules' => 'required|min_length[6]|differs[current_password]',
                'errors' => [
                    'differs' => 'La nueva contraseña debe ser diferente a la actual'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirmación',
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'matches' => 'Las contraseñas no coinciden'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->to('password')
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Proceso de cambio de contraseña
        $userModel = new UsuarioModel();
        $user = $userModel->find(session('id_usuario'));

        // Verificar contraseña actual
        if (!password_verify($this->request->getPost('current_password'), $user['contrasena'])) {
            return redirect()->to('password')
                ->with('error', 'La contraseña actual es incorrecta');
        }

        // Actualizar contraseña
        $userModel->update($user['id_usuario'], [
            'contrasena' => password_hash(
                $this->request->getPost('new_password'),
                PASSWORD_DEFAULT
            )
        ]);

        // Obtener datos actualizados del usuario
        $updatedUser = $userModel->find($user['id_usuario']);

        // Enviar notificación por email
        $this->sendPasswordChangedEmail($updatedUser);

        return redirect()->to('password')
            ->with('success', 'Contraseña actualizada exitosamente');
    }

    private function sendPasswordChangedEmail($user)
    {
        try {
            if (!filter_var($user['correo'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Email inválido: {$user['correo']}");
            }

            $resend = new ResendService();

            $emailData = [
                'nombre' => $user['nombre'] ?? 'Usuario',
                'fecha' => date('d/m/Y H:i'),
                'ip' => $this->request->getIPAddress()
            ];

            $response = $resend->sendEmail(
                $user['correo'],
                'Contraseña actualizada - ' . date('d/m/Y H:i'),
                view('emails/password_cambiada', $emailData)
            );

            if (!$response) {
                log_message('error', 'Resend no devolvió respuesta');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error al enviar email: ' . $e->getMessage());
        }
    }
}
