<?php

namespace App\Libraries;

use Resend;
use Config\Resend as ResendConfig;
use CodeIgniter\Config\BaseConfig;

class ResendService
{
    protected $client;
    protected $config;


    public function __construct()
    {
        // Cargar configuración corregida
        $this->config = config('Resend');

        // Validar que existan los valores
        if (empty($this->config->apiKey)) {
            throw new \RuntimeException('Resend API Key no configurada');
        }

        $this->client = Resend::client($this->config->apiKey);
    }

    public function sendEmail($to, $subject, $html)
    {
        try {
            $response = $this->client->emails->send([
                'from' => "{$this->config->fromName} <{$this->config->fromEmail}>",
                'to' => (array) $to,
                'subject' => $subject,
                'html' => $html
            ]);

            // Log detallado
            log_message('info', '[Resend] Email enviado a: ' . print_r($to, true));
            log_message('debug', '[Resend Response] ' . print_r($response, true));

            return $response;
        } catch (\Exception $e) {
            log_message('error', '[Resend Error] ' . $e->getMessage());
            log_message('debug', '[Resend Error Details] ' . $e->getTraceAsString());
            return false;
        }
    }
}
