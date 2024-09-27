<?php
namespace App\Services;

interface QrCodeServiceInterface 
{
    
public function generatePdf(string $view, array $data, string $pdfPath);
}