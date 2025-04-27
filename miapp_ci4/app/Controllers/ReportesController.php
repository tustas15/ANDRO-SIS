<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\ProyectoModel;
use App\Models\PublicacionModel;
use App\Models\CategoriaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class ReportesController extends BaseController
{
    protected $usuarioModel;
    protected $proyectoModel;
    protected $publicacionModel;
    protected $categoriaModel;

    public function __construct()
    {   
        while (ob_get_level()) {
            ob_end_clean();
        }
        $this->usuarioModel = new UsuarioModel();
        $this->proyectoModel = new ProyectoModel();
        $this->publicacionModel = new PublicacionModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function index($tipoReporte = 'contratistas')
    {
        $data = [
            'titulo' => 'Reportes del Sistema',
            'tipoReporte' => $tipoReporte,
            'estadisticasGlobales' => $this->obtenerEstadisticasGlobales()
        ];

        switch ($tipoReporte) {
            case 'contratistas':
                $data['contratistas'] = $this->generarReporteContratistas();
                break;

            case 'proyectos':
                $data['proyectos'] = $this->generarReporteProyectos();
                break;

            case 'categorias':
                $data['categorias'] = $this->generarReporteCategorias();
                break;

            default:
                return redirect()->back()->with('error', 'Tipo de reporte inválido');
        }

        return view('admin/reportes', $data);
    }

    protected function generarReporteContratistas()
    {
        $contratistas = $this->usuarioModel->getContratistasConEstadisticasCompletas();

        foreach ($contratistas as &$contratista) {
            $contratista['presupuesto_total'] = $this->proyectoModel->getTotalBudget($contratista['id_usuario']);
            $contratista['total_comentarios'] = $this->publicacionModel->getTotalCommentsByContractor($contratista['id_usuario']);
        }

        return $contratistas;
    }

    protected function generarReporteProyectos()
    {
        return $this->proyectoModel->select('proyecto.*, 
        usuarios.nombre as contratista, 
        categorias.nombre as categoria,
        COUNT(publicacion.id_publicacion) as total_publicaciones,
        COALESCE(SUM(publicacion.peso), 0) as avance')
            ->join('usuarios', 'usuarios.id_usuario = proyecto.id_contratista')
            ->join('categorias', 'categorias.id_categoria = proyecto.id_categoria')
            ->join('publicacion', 'publicacion.id_proyectos = proyecto.id_proyectos', 'left')
            ->groupBy('proyecto.id_proyectos')
            ->findAll();
    }

    protected function generarReporteCategorias()
    {
        return $this->categoriaModel->getCategoriasConEstadisticas();
    }

    protected function obtenerEstadisticasGlobales()
    {
        return [
            'total_contratistas' => $this->usuarioModel->where('perfil', 'contratista')->countAllResults(),
            'total_proyectos' => $this->proyectoModel->countAll(),
            'presupuesto_total' => $this->proyectoModel->selectSum('presupuesto')->first()['presupuesto']
        ];
    }

    public function exportarPDF($tipoReporte)
    {
        $data = $this->obtenerDatosReporte($tipoReporte);
        $html = view('admin/reportes/plantilla_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Limpiar buffer de salida
        if (ob_get_length()) ob_clean();

        $dompdf->stream("reporte_{$tipoReporte}.pdf", [
            "Attachment" => true,
            "isRemoteEnabled" => true
        ]);
        exit();
    }

    public function exportarExcel($tipoReporte)
    {
        $data = $this->obtenerDatosReporte($tipoReporte);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Estilos
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFD9D9D9']]
        ];

        switch ($tipoReporte) {
            case 'contratistas':
                $this->generarExcelContratistas($sheet, $data, $headerStyle);
                break;

            case 'proyectos':
                $this->generarExcelProyectos($sheet, $data, $headerStyle);
                break;

            case 'categorias':
                $this->generarExcelCategorias($sheet, $data, $headerStyle);
                break;
        }

        while (ob_get_level()) {
            ob_end_clean();
        }
    

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_' . $tipoReporte . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    private function obtenerDatosReporte($tipoReporte)
    {
        $data = [
            'titulo' => 'Reporte de ' . ucfirst($tipoReporte),
            'fecha' => date('d/m/Y H:i:s'),
            'estadisticasGlobales' => $this->obtenerEstadisticasGlobales()
        ];

        switch ($tipoReporte) {
            case 'contratistas':
                $data['contratistas'] = $this->generarReporteContratistas();
                break;

            case 'proyectos':
                $data['proyectos'] = $this->generarReporteProyectos();
                break;

            case 'categorias':
                $data['categorias'] = $this->generarReporteCategorias();
                break;
        }

        return $data;
    }

    private function generarExcelContratistas($sheet, $data, $headerStyle)
    {
        // Encabezados
        $sheet->setCellValue('A1', 'Reporte de Contratistas');
        $sheet->mergeCells('A1:F1');
        $sheet->fromArray(
            ['Nombre', 'Proyectos', 'Publicaciones', 'Likes', 'Comentarios', 'Presupuesto Total'],
            null,
            'A3'
        );

        // Datos
        $row = 4;
        foreach ($data['contratistas'] as $c) {
            $sheet->setCellValue('A' . $row, $c['nombre'] . ' ' . $c['apellido'])
                ->setCellValue('B' . $row, $c['total_proyectos'])
                ->setCellValue('C' . $row, $c['total_publicaciones'])
                ->setCellValue('D' . $row, $c['total_megustas'])
                ->setCellValue('E' . $row, $c['total_comentarios'])
                ->setCellValue('F' . $row, $c['presupuesto_total']);
            $row++;
        }

        // Aplicar estilos
        $sheet->getStyle('A3:F3')->applyFromArray($headerStyle);
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelProyectos($sheet, $data, $headerStyle)
    {
        $sheet->setCellValue('A1', 'Reporte de Proyectos');
        $sheet->mergeCells('A1:F1');
        $sheet->fromArray(
            ['Proyecto', 'Contratista', 'Categoría', 'Presupuesto', 'Avance', 'Publicaciones'],
            null,
            'A3'
        );

        $row = 4;
        foreach ($data['proyectos'] as $p) {
            $sheet->setCellValue('A' . $row, $p['titulo'])
                ->setCellValue('B' . $row, $p['contratista'])
                ->setCellValue('C' . $row, $p['categoria'])
                ->setCellValue('D' . $row, $p['presupuesto'])
                ->setCellValue('E' . $row, $p['avance'])
                ->setCellValue('F' . $row, $p['total_publicaciones']);
            $row++;
        }

        $sheet->getStyle('A3:F3')->applyFromArray($headerStyle);
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function generarExcelCategorias($sheet, $data, $headerStyle)
    {
        $sheet->setCellValue('A1', 'Reporte de Categorías');
        $sheet->mergeCells('A1:D1');
        $sheet->fromArray(
            ['Categoría', 'Proyectos', 'Presupuesto Total', 'Avance Promedio'],
            null,
            'A3'
        );

        $row = 4;
        foreach ($data['categorias'] as $cat) {
            $sheet->setCellValue('A' . $row, $cat['nombre'])
                ->setCellValue('B' . $row, $cat['total_proyectos'])
                ->setCellValue('C' . $row, $cat['presupuesto_total'])
                ->setCellValue('D' . $row, $cat['promedio_avance']);
            $row++;
        }

        $sheet->getStyle('A3:D3')->applyFromArray($headerStyle);
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
