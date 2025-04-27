<?php namespace App\Models;

use CodeIgniter\Model;

class ArchivoAdjuntoModel extends Model
{
    protected $table = 'archivos_adjuntos';
    protected $primaryKey = 'id_archivo';
    protected $allowedFields = ['id_mensaje', 'ruta_archivo', 'tipo'];
    protected $useTimestamps = false;

    // Relación con la tabla mensajes
    public function mensaje()
    {
        return $this->belongsTo('App\Models\MensajeModel', 'id_mensaje', 'id_mensaje');
    }

    // Método para obtener el tipo de archivo como icono
    public function getIcono($tipo)
    {
        $iconos = [
            'imagen'    => 'fa-file-image',
            'documento' => 'fa-file-pdf',
            'otro'      => 'fa-file'
        ];

        return $iconos[$tipo] ?? 'fa-file';
    }
}