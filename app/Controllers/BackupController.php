<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Database;

class BackupController extends BaseController
{
    protected $db;
    protected $backupPath;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->backupPath = WRITEPATH . 'backups/';

        // Crear directorio si no existe
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
            file_put_contents($this->backupPath . '.htaccess', "Deny from all");
        }
    }

    public function index()
    {
        $backups = [];
        if (is_dir($this->backupPath)) {
            $files = array_reverse(scandir($this->backupPath));
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && !is_dir($this->backupPath . $file)) {
                    $backups[] = [
                        'name' => $file,
                        'size' => human_filesize(filesize($this->backupPath . $file)),
                        'date' => date('Y-m-d H:i:s', filemtime($this->backupPath . $file))
                    ];
                }
            }
        }

        return view('admin/backup', [
            'backups' => $backups,
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function createBackup()
    {
        try {
            $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
            $path = $this->backupPath . $filename;

            // Obtener todas las tablas
            $tables = $this->db->listTables();
            $sqlScript = "-- Backup generado el " . date('Y-m-d H:i:s') . "\n\n";

            foreach ($tables as $table) {
                // 1. Obtener estructura de la tabla
                $query = $this->db->query("SHOW CREATE TABLE `$table`");
                $row = $query->getRowArray();
                $sqlScript .= "-- Estructura para tabla `$table`\n";
                $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
                $sqlScript .= $row['Create Table'] . ";\n\n";

                // 2. Obtener datos de la tabla
                $dataQuery = $this->db->query("SELECT * FROM `$table`");
                $rows = $dataQuery->getResultArray();

                if (!empty($rows)) {
                    $sqlScript .= "-- Volcado de datos para tabla `$table`\n";
                    $columns = array_keys($rows[0]);

                    foreach ($rows as $row) {
                        $escapedValues = array_map(function ($value) {
                            return $this->db->escape($value);
                        }, array_values($row));

                        $sqlScript .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) 
                                 VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                    $sqlScript .= "\n";
                }
            }

            // 3. Escribir archivo
            if (file_put_contents($path, $sqlScript) !== false) {
                return redirect()->back()->with('success', 'Respaldo creado');
            }

            throw new \Exception("Error al escribir el archivo");
        } catch (\Exception $e) {
            log_message('error', 'BACKUP MANUAL ERROR: ' . $e->getMessage());
            return redirect()->back()->with('error', "Error: " . $e->getMessage());
        }
    }

    public function restoreBackup()
    {
        $file = $this->request->getFile('backup_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Archivo inválido');
        }

        try {
            $sql = file_get_contents($file->getTempName());

            // Normalizar saltos de línea
            $sql = str_replace(["\r\n", "\r"], "\n", $sql);

            // Dividir consultas conservando DELIMITER
            $queries = [];
            $delimiter = ';';
            $buffer = '';

            foreach (explode("\n", $sql) as $line) {
                if (preg_match('/^DELIMITER\s+(\S+)/i', $line, $match)) {
                    $delimiter = $match[1];
                    continue;
                }

                $buffer .= $line . "\n";

                if (substr(rtrim($line), -strlen($delimiter)) === $delimiter) {
                    $queries[] = substr($buffer, 0, -strlen($delimiter) - 1);
                    $buffer = '';
                }
            }

            $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
            $this->db->transStart();

            foreach ($queries as $index => $query) {
                $query = trim($query);
                if (!empty($query)) {
                    try {
                        $this->db->query($query);
                    } catch (\Exception $e) {
                        $errorMessage = "Error en línea " . ($index + 1) . ": " . $e->getMessage();
                        log_message('error', $errorMessage . "\nQuery: " . $query);
                        throw new \Exception($errorMessage);
                    }
                }
            }

            $this->db->transComplete();
            $this->db->query('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'Restauración exitosa');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteBackup($filename)
    {
        $path = $this->backupPath . basename($filename);

        if (file_exists($path) && unlink($path)) {
            return redirect()->back()->with('success', 'Respaldo eliminado');
        }

        return redirect()->back()->with('error', 'Error al eliminar el respaldo');
    }

    public function downloadBackup($filename)
    {
        $path = $this->backupPath . basename($filename);

        if (file_exists($path)) {
            return $this->response->download($path, null);
        }

        return redirect()->back()->with('error', 'Archivo no encontrado');
    }
}

// Función helper para formato de tamaño
if (!function_exists('human_filesize')) {
    function human_filesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
