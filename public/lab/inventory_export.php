<?php
define('APP_ACCESS', true);
require_once __DIR__ . '/app/config.php';

// Get filter parameters (same as inventory_list.php)
$lab_filter = $_GET['lab'] ?? 'all';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$condition_filter = $_GET['condition'] ?? '';
$search = $_GET['search'] ?? '';
$export_format = $_GET['format'] ?? 'xlsx'; // xlsx, csv, pdf, docx

try {
    // Get database connection
    $db = getDB();
    
    // Build query (same as inventory_list.php)
    $sql = "SELECT 
                li.*,
                r.name AS lab_name,
                r.id AS lab_id,
                u1.full_name AS created_by_name,
                u2.full_name AS updated_by_name
            FROM lab_inventory li
            JOIN resources r ON li.resource_id = r.id
            LEFT JOIN users u1 ON li.created_by = u1.id
            LEFT JOIN users u2 ON li.updated_by = u2.id
            WHERE li.deleted_at IS NULL";

    $params = [];

    // Add filters
    if (!empty($lab_filter) && $lab_filter !== 'all') {
        $sql .= " AND li.resource_id = :lab_id";
        $params[':lab_id'] = $lab_filter;
    }

    if (!empty($category_filter)) {
        $sql .= " AND li.category = :category";
        $params[':category'] = $category_filter;
    }

    if (!empty($status_filter)) {
        $sql .= " AND li.status = :status";
        $params[':status'] = $status_filter;
    }

    if (!empty($condition_filter)) {
        $sql .= " AND li.`condition` = :condition";
        $params[':condition'] = $condition_filter;
    }

    if (!empty($search)) {
        $sql .= " AND (li.item_name LIKE :search OR li.brand LIKE :search OR li.model LIKE :search OR li.serial_number LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY r.name, li.category, li.item_name";

    // Execute query
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $inventories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all labs for multi-sheet export
    $labs_stmt = $db->query("SELECT id, name FROM resources WHERE type = 'lab' AND deleted_at IS NULL ORDER BY name");
    $all_labs = $labs_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get lab name for filename
    $lab_name = 'Semua Lab';
    if (!empty($lab_filter) && $lab_filter !== 'all') {
        $lab_stmt = $db->prepare("SELECT name FROM resources WHERE id = :id");
        $lab_stmt->execute([':id' => $lab_filter]);
        $lab_result = $lab_stmt->fetch(PDO::FETCH_ASSOC);
        if ($lab_result) {
            $lab_name = $lab_result['name'];
        }
    }

    // Generate filename
    $filename = 'Inventaris_' . str_replace(' ', '_', $lab_name) . '_' . date('Y-m-d_His');

    // Export based on format
    if ($export_format === 'csv') {
        exportToCSV($inventories, $filename);
    } else if ($export_format === 'pdf') {
        exportToPDF($inventories, $filename, $lab_name, $lab_filter, $all_labs);
    } else if ($export_format === 'docx') {
        exportToWord($inventories, $filename, $lab_name, $lab_filter, $all_labs);
    } else {
        // Group data by lab_id for efficient Excel processing
        $grouped_data = [];
        foreach ($inventories as $item) {
            $grouped_data[$item['lab_id']][] = $item;
        }
        exportToExcel($grouped_data, $inventories, $filename, $lab_name, $lab_filter, $all_labs, $db);
    }

} catch (PDOException $e) {
    error_log("Database error in inventory_export.php: " . $e->getMessage());
    die("Error: Tidak dapat mengambil data. Silakan coba lagi.");
} catch (Exception $e) {
    error_log("Export error: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}

/**
 * Export to Word (.docx) format
 */
function exportToWord($data, $filename, $lab_name, $lab_filter, $all_labs) {
    // Fallback to HTML print view
    exportToHTMLPrint($data, $filename, $lab_name, $lab_filter, $all_labs, true);
    return;
}

/**
 * Export to Excel using PhpSpreadsheet
 */
function exportToExcel($grouped_data, $all_inventories, $filename, $lab_name, $lab_filter, $all_labs, $db) {
    // Load autoload
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        // If vendor not available, fallback to CSV
        exportToCSV($all_inventories, $filename);
        return;
    }
    
    require_once $autoload;
    
    // Check if PhpSpreadsheet is available
    if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        // If not available, fallback to CSV
        exportToCSV($all_inventories, $filename);
        return;
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator(APP_NAME)
        ->setTitle('Inventaris Lab Komputer')
        ->setSubject('Laporan Inventaris')
        ->setDescription('Laporan inventaris ' . $lab_name);

    // Check if "Semua Lab" selected
    if ($lab_filter === 'all' && !empty($all_labs)) {
        // MULTIPLE SHEETS - One per lab + Summary
        $sheetIndex = 0;
        
        foreach ($all_labs as $lab) {
            // Get data for this specific lab from pre-grouped data
            $lab_data = $grouped_data[$lab['id']] ?? [];
            
            // Create sheet
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            
            $sheet->setTitle(substr($lab['name'], 0, 31)); // Excel max 31 chars for sheet name
            
            // Add content to sheet
            createLabSheet($sheet, $lab_data, $lab['name']);
            
            $sheetIndex++;
        }
        
        // Add Summary Sheet
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Ringkasan');
        createSummarySheet($summarySheet, $all_labs, $grouped_data);
        
        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        
    } else {
        // SINGLE SHEET - Normal export for single lab
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($lab_name, 0, 31));
        createLabSheet($sheet, $all_inventories, $lab_name);
    }

    // Output
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

/**
 * Create individual lab sheet
 */
function createLabSheet($sheet, $data, $lab_name) {
    // Title
    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'LAPORAN INVENTARIS LAB KOMPUTER');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $sheet->mergeCells('A2:I2');
    $sheet->setCellValue('A2', $lab_name);
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $sheet->mergeCells('A3:I3');
    $sheet->setCellValue('A3', 'Tanggal: ' . date('d/m/Y H:i:s'));
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Headers - simplified columns
    $headers = [
        'No',
        'Nama Item',
        'Brand',
        'Spesifikasi',
        'Total Unit',
        'Unit Baik',
        'Unit Rusak',
        'Unit Cadangan',
        'Catatan'
    ];
    
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '5', $header);
        $col++;
    }
    
    // Style header
    $sheet->getStyle('A5:I5')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '667eea']
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
        ],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
    ]);

    // Data
    $row = 6;
    $no = 1;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $no);
        $sheet->setCellValue('B' . $row, $item['item_name']);
        $sheet->setCellValue('C' . $row, $item['brand'] ?? '-');
        $sheet->setCellValue('D' . $row, $item['specifications'] ?? '-');
        $sheet->setCellValue('E' . $row, $item['quantity']);
        $sheet->setCellValue('F' . $row, $item['quantity_good']);
        $sheet->setCellValue('G' . $row, $item['quantity_broken']);
        $sheet->setCellValue('H' . $row, $item['quantity_backup']);
        $sheet->setCellValue('I' . $row, $item['notes'] ?? '-');
        
        $row++;
        $no++;
    }

    // Auto-size columns
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Borders for data
    if ($row > 6) {
        $sheet->getStyle('A5:I' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
            ]
        ]);
    }

    // Summary at bottom
    $row += 2;
    $total_items = count($data);
    $total_quantity = array_sum(array_column($data, 'quantity'));
    $total_good = array_sum(array_column($data, 'quantity_good'));
    $total_broken = array_sum(array_column($data, 'quantity_broken'));
    $total_backup = array_sum(array_column($data, 'quantity_backup'));
    
    $sheet->setCellValue('A' . $row, 'RINGKASAN:');
    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
    $row++;
    
    $sheet->setCellValue('A' . $row, 'Total Item:');
    $sheet->setCellValue('B' . $row, $total_items);
    $row++;
    
    $sheet->setCellValue('A' . $row, 'Total Unit:');
    $sheet->setCellValue('B' . $row, $total_quantity);
    $row++;
    
    $sheet->setCellValue('A' . $row, 'Unit Baik:');
    $sheet->setCellValue('B' . $row, $total_good);
    $row++;
    
    $sheet->setCellValue('A' . $row, 'Unit Rusak:');
    $sheet->setCellValue('B' . $row, $total_broken);
    $row++;
    
    $sheet->setCellValue('A' . $row, 'Unit Cadangan:');
    $sheet->setCellValue('B' . $row, $total_backup);
    
    $sheet->getStyle('A' . ($row - 5) . ':B' . $row)->getFont()->setBold(true);
}

/**
 * Create summary sheet for all labs
 */
function createSummarySheet($sheet, $all_labs, $grouped_data) {
    // Title
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue('A1', 'RINGKASAN INVENTARIS SEMUA LAB');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $sheet->mergeCells('A2:F2');
    $sheet->setCellValue('A2', 'Tanggal: ' . date('d/m/Y H:i:s'));
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Headers
    $headers = ['Lab', 'Total Item', 'Total Unit', 'Unit Baik', 'Unit Rusak', 'Unit Cadangan'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '4', $header);
        $col++;
    }
    
    // Style header
    $sheet->getStyle('A4:F4')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '667eea']
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
        ],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
    ]);

    // Data for each lab
    $row = 5;
    $grand_total_items = 0;
    $grand_total_quantity = 0;
    $grand_total_good = 0;
    $grand_total_broken = 0;
    $grand_total_backup = 0;
    
    foreach ($all_labs as $lab) {
        $lab_data = $grouped_data[$lab['id']] ?? [];
        
        $total_items = count($lab_data);
        $total_quantity = array_sum(array_column($lab_data, 'quantity'));
        $total_good = array_sum(array_column($lab_data, 'quantity_good'));
        $total_broken = array_sum(array_column($lab_data, 'quantity_broken'));
        $total_backup = array_sum(array_column($lab_data, 'quantity_backup'));
        
        $sheet->setCellValue('A' . $row, $lab['name']);
        $sheet->setCellValue('B' . $row, $total_items);
        $sheet->setCellValue('C' . $row, $total_quantity);
        $sheet->setCellValue('D' . $row, $total_good);
        $sheet->setCellValue('E' . $row, $total_broken);
        $sheet->setCellValue('F' . $row, $total_backup);
        
        $grand_total_items += $total_items;
        $grand_total_quantity += $total_quantity;
        $grand_total_good += $total_good;
        $grand_total_broken += $total_broken;
        $grand_total_backup += $total_backup;
        
        $row++;
    }
    
    // Grand Total Row
    $sheet->setCellValue('A' . $row, 'GRAND TOTAL');
    $sheet->setCellValue('B' . $row, $grand_total_items);
    $sheet->setCellValue('C' . $row, $grand_total_quantity);
    $sheet->setCellValue('D' . $row, $grand_total_good);
    $sheet->setCellValue('E' . $row, $grand_total_broken);
    $sheet->setCellValue('F' . $row, $grand_total_backup);
    
    $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E0E0E0']
        ]
    ]);
    
    // Auto-size columns
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Borders
    $sheet->getStyle('A4:F' . $row)->applyFromArray([
        'borders' => [
            'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
        ]
    ]);
}

/**
 * Export to CSV
 */
function exportToCSV($data, $filename) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
    header('Cache-Control: max-age=0');

    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Headers - simplified columns
    fputcsv($output, [
        'No',
        'Nama Item',
        'Brand',
        'Spesifikasi',
        'Total Unit',
        'Unit Baik',
        'Unit Rusak',
        'Unit Cadangan',
        'Catatan'
    ]);

    // Data
    $no = 1;
    foreach ($data as $item) {
        fputcsv($output, [
            $no,
            $item['item_name'],
            $item['brand'] ?? '-',
            $item['specifications'] ?? '-',
            $item['quantity'],
            $item['quantity_good'],
            $item['quantity_broken'],
            $item['quantity_backup'],
            $item['notes'] ?? '-'
        ]);
        $no++;
    }

    fclose($output);
    exit;
}

/**
 * Export to PDF
 */
function exportToPDF($data, $filename, $lab_name, $lab_filter, $all_labs) {
    // Load autoload
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        // Fallback to HTML print view
        exportToHTMLPrint($data, $filename, $lab_name, $lab_filter, $all_labs, true);
        return;
    }
    
    require_once $autoload;
    
    // Check if TCPDF or similar library is available
    if (!class_exists('TCPDF')) {
        // Fallback to HTML print view
        exportToHTMLPrint($data, $filename, $lab_name, $lab_filter, $all_labs, true);
        return;
    }
    
    $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(APP_NAME);
    $pdf->SetAuthor(APP_NAME);
    $pdf->SetTitle('Inventaris Lab Komputer');
    $pdf->SetSubject('Laporan Inventaris');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);

    // Check if "Semua Lab" - create sections per lab
    if ($lab_filter === 'all' && !empty($all_labs)) {
        foreach ($all_labs as $index => $lab) {
            // Get data for this lab
            global $db;
            $sql = "SELECT 
                        li.*,
                        r.name AS lab_name
                    FROM lab_inventory li
                    JOIN resources r ON li.resource_id = r.id
                    WHERE li.deleted_at IS NULL 
                    AND li.resource_id = :lab_id
                    ORDER BY li.category, li.item_name";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([':lab_id' => $lab['id']]);
            $lab_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add page for this lab
            $pdf->AddPage();
            createLabPDF($pdf, $lab_data, $lab['name']);
        }
    } else {
        // Single lab
        $pdf->AddPage();
        createLabPDF($pdf, $data, $lab_name);
    }

    // Output
    $pdf->Output($filename . '.pdf', 'D');
    exit;
}

/**
 * Create PDF content for a lab
 */
function createLabPDF($pdf, $data, $lab_name) {
    // Title
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 8, 'LAPORAN INVENTARIS LAB KOMPUTER', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 6, $lab_name, 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(0, 5, 'Tanggal: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
    $pdf->Ln(3);

    // Table
    $pdf->SetFont('helvetica', '', 8);
    
    // Build HTML table
    $html = '<style>
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #667eea; color: #ffffff; font-weight: bold; padding: 5px; border: 1px solid #333; text-align: center; font-size: 8pt; }
        td { padding: 4px; border: 1px solid #cccccc; font-size: 7pt; }
    </style>
    <table cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th width="5%"><b>No</b></th>
                <th width="20%"><b>Nama Item</b></th>
                <th width="12%"><b>Brand</b></th>
                <th width="18%"><b>Spesifikasi</b></th>
                <th width="7%"><b>Total</b></th>
                <th width="7%"><b>Baik</b></th>
                <th width="7%"><b>Rusak</b></th>
                <th width="7%"><b>Cadangan</b></th>
                <th width="17%"><b>Catatan</b></th>
            </tr>
        </thead>
        <tbody>';

    if (empty($data)) {
        $html .= '<tr><td colspan="9" style="text-align: center; padding: 20px;">Tidak ada data inventaris</td></tr>';
    } else {
        $no = 1;
        foreach ($data as $item) {
            $notes = $item['notes'] ?? '-';
            if (strlen($notes) > 40) {
                $notes = substr($notes, 0, 37) . '...';
            }
            
            $specs = $item['specifications'] ?? '-';
            if (strlen($specs) > 50) {
                $specs = substr($specs, 0, 47) . '...';
            }
            
            $html .= '<tr>
                <td align="center">' . $no . '</td>
                <td>' . htmlspecialchars($item['item_name']) . '</td>
                <td>' . htmlspecialchars($item['brand'] ?? '-') . '</td>
                <td>' . htmlspecialchars($specs) . '</td>
                <td align="center">' . htmlspecialchars($item['quantity']) . '</td>
                <td align="center">' . htmlspecialchars($item['quantity_good']) . '</td>
                <td align="center">' . htmlspecialchars($item['quantity_broken']) . '</td>
                <td align="center">' . htmlspecialchars($item['quantity_backup']) . '</td>
                <td>' . htmlspecialchars($notes) . '</td>
            </tr>';
            $no++;
        }
    }

    $html .= '</tbody></table>';
    
    // Add summary
    $total_items = count($data);
    $total_quantity = array_sum(array_column($data, 'quantity'));
    $total_good = array_sum(array_column($data, 'quantity_good'));
    $total_broken = array_sum(array_column($data, 'quantity_broken'));
    $total_backup = array_sum(array_column($data, 'quantity_backup'));
    
    $html .= '<br><br><table style="width: 40%;">
        <tr><td><b>RINGKASAN:</b></td></tr>
        <tr><td><b>Total Item:</b> ' . $total_items . '</td></tr>
        <tr><td><b>Total Unit:</b> ' . $total_quantity . '</td></tr>
        <tr><td><b>Unit Baik:</b> ' . $total_good . '</td></tr>
        <tr><td><b>Unit Rusak:</b> ' . $total_broken . '</td></tr>
        <tr><td><b>Unit Cadangan:</b> ' . $total_backup . '</td></tr>
    </table>';

    // Write HTML
    $pdf->writeHTML($html, true, false, true, false, '');
}

/**
 * Export to HTML Print View (fallback for PDF and Word)
 */
function exportToHTMLPrint($data, $filename, $lab_name, $lab_filter, $all_labs, $with_signature = false) {
    // Calculate summary
    $total_items = count($data);
    $total_quantity = array_sum(array_column($data, 'quantity'));
    $total_good = array_sum(array_column($data, 'quantity_good'));
    $total_broken = array_sum(array_column($data, 'quantity_broken'));
    $total_backup = array_sum(array_column($data, 'quantity_backup'));
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetak Inventaris - <?= htmlspecialchars($lab_name) ?></title>
        <style>
            @media print {
                .no-print { display: none; }
                @page { 
                    size: landscape;
                    margin: 1cm;
                }
                .page-break { page-break-before: always; }
            }
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            h1, h2 { text-align: center; margin: 5px 0; }
            h1 { font-size: 18px; }
            h2 { font-size: 14px; }
            .date { text-align: center; font-size: 12px; margin-bottom: 20px; }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                font-size: 10px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
            }
            th {
                background-color: #667eea;
                color: white;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .btn {
                padding: 10px 20px;
                margin: 10px 5px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }
            .summary {
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 5px;
            }
            .summary table {
                width: auto;
                font-size: 12px;
            }
            .summary td {
                border: none;
                padding: 5px 15px;
            }
            .text-center { text-align: center; }
            
            /* Signature section */
            .signature-section {
                margin-top: 40px;
                text-align: right;
            }
            .signature-box {
                display: inline-block;
                text-align: center;
                min-width: 200px;
            }
            .signature-line {
                border-top: 1px solid #000;
                margin-top: 60px;
                padding-top: 5px;
            }
        </style>
    </head>
    <body>
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button class="btn" onclick="window.print()">🖨️ Cetak / Save as PDF</button>
            <button class="btn" onclick="window.close()" style="background: #6c757d;">✖️ Tutup</button>
        </div>
        
        <?php if ($lab_filter === 'all' && !empty($all_labs)): ?>
            <?php foreach ($all_labs as $index => $lab): ?>
                <?php
                // Get data for this lab
                global $db;
                $sql = "SELECT 
                            li.*,
                            r.name AS lab_name
                        FROM lab_inventory li
                        JOIN resources r ON li.resource_id = r.id
                        WHERE li.deleted_at IS NULL 
                        AND li.resource_id = :lab_id
                        ORDER BY li.category, li.item_name";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([':lab_id' => $lab['id']]);
                $lab_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $lab_total_items = count($lab_data);
                $lab_total_quantity = array_sum(array_column($lab_data, 'quantity'));
                $lab_total_good = array_sum(array_column($lab_data, 'quantity_good'));
                $lab_total_broken = array_sum(array_column($lab_data, 'quantity_broken'));
                $lab_total_backup = array_sum(array_column($lab_data, 'quantity_backup'));
                ?>
                
                <?php if ($index > 0): ?>
                    <div class="page-break"></div>
                <?php endif; ?>
                
                <h1>LAPORAN INVENTARIS LAB KOMPUTER</h1>
                <h2><?= htmlspecialchars($lab['name']) ?></h2>
                <p class="date">Tanggal: <?= date('d/m/Y H:i:s') ?></p>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Nama Item</th>
                            <th style="width: 12%;">Brand</th>
                            <th style="width: 18%;">Spesifikasi</th>
                            <th style="width: 7%;">Total</th>
                            <th style="width: 7%;">Baik</th>
                            <th style="width: 7%;">Rusak</th>
                            <th style="width: 7%;">Cadangan</th>
                            <th style="width: 17%;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lab_data)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data inventaris</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($lab_data as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no ?></td>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['brand'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($item['specifications'] ?? '-') ?></td>
                                <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($item['quantity_good']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($item['quantity_broken']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($item['quantity_backup']) ?></td>
                                <td><?= htmlspecialchars($item['notes'] ?? '-') ?></td>
                            </tr>
                            <?php $no++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="summary">
                    <strong>RINGKASAN:</strong>
                    <table>
                        <tr><td><strong>Total Item:</strong></td><td><?= $lab_total_items ?></td></tr>
                        <tr><td><strong>Total Unit:</strong></td><td><?= $lab_total_quantity ?></td></tr>
                        <tr><td><strong>Unit Baik:</strong></td><td><?= $lab_total_good ?></td></tr>
                        <tr><td><strong>Unit Rusak:</strong></td><td><?= $lab_total_broken ?></td></tr>
                        <tr><td><strong>Unit Cadangan:</strong></td><td><?= $lab_total_backup ?></td></tr>
                    </table>
                </div>
                
                <?php if ($with_signature): ?>
                <div class="signature-section">
                    <div class="signature-box">
                        <p>Surabaya, <?= date('d F Y') ?></p>
                        <p>Penanggung Jawab,</p>
                        <div class="signature-line">
                            ( ............................... )
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Single Lab -->
            <h1>LAPORAN INVENTARIS LAB KOMPUTER</h1>
            <h2><?= htmlspecialchars($lab_name) ?></h2>
            <p class="date">Tanggal: <?= date('d/m/Y H:i:s') ?></p>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Nama Item</th>
                        <th style="width: 12%;">Brand</th>
                        <th style="width: 18%;">Spesifikasi</th>
                        <th style="width: 7%;">Total</th>
                        <th style="width: 7%;">Baik</th>
                        <th style="width: 7%;">Rusak</th>
                        <th style="width: 7%;">Cadangan</th>
                        <th style="width: 17%;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data inventaris</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($data as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['brand'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['specifications'] ?? '-') ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['quantity_good']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['quantity_broken']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['quantity_backup']) ?></td>
                            <td><?= htmlspecialchars($item['notes'] ?? '-') ?></td>
                        </tr>
                        <?php $no++; endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="summary">
                <strong>RINGKASAN:</strong>
                <table>
                    <tr><td><strong>Total Item:</strong></td><td><?= $total_items ?></td></tr>
                    <tr><td><strong>Total Unit:</strong></td><td><?= $total_quantity ?></td></tr>
                    <tr><td><strong>Unit Baik:</strong></td><td><?= $total_good ?></td></tr>
                    <tr><td><strong>Unit Rusak:</strong></td><td><?= $total_broken ?></td></tr>
                    <tr><td><strong>Unit Cadangan:</strong></td><td><?= $total_backup ?></td></tr>
                </table>
            </div>
            
            <?php if ($with_signature): ?>
            <div class="signature-section">
                <div class="signature-box">
                    <p>Surabaya, <?= date('d F Y') ?></p>
                    <p>Penanggung Jawab,</p>
                    <div class="signature-line">
                        ( ............................... )
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </body>
    </html>
    <?php
    exit;
}
?>