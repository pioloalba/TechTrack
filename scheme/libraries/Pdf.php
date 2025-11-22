<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Simple PDF Generator using FPDF
 * Download FPDF from http://www.fpdf.org/
 * For now, this will generate HTML that can be converted to PDF client-side
 */
class Pdf
{
    protected $lava;
    
    public function __construct()
    {
        $this->lava =& lava_instance();
    }
    
    /**
     * Generate PDF from HTML content
     * Uses HTML2PDF approach for browser printing
     */
    public function generateFromHTML($html, $filename = 'report.pdf', $orientation = 'P', $format = 'A4')
    {
        // Set headers for PDF download
        header('Content-Type: text/html; charset=utf-8');
        
        // Return printable HTML with PDF styling
        $printableHTML = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($filename) . '</title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none !important; }
            @page { size: ' . $format . ' ' . ($orientation == 'P' ? 'portrait' : 'landscape') . '; margin: 15mm; }
        }
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        h1 { color: #333; border-bottom: 2px solid #3B82F6; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .stat-box { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .stat-label { color: #666; font-size: 10pt; }
        .stat-value { font-size: 18pt; font-weight: bold; color: #333; }
    </style>
    <script>
        window.onload = function() {
            window.print();
            // Optional: close window after printing
            // setTimeout(function(){ window.close(); }, 100);
        };
    </script>
</head>
<body>
' . $html . '
</body>
</html>';
        
        return $printableHTML;
    }
}
