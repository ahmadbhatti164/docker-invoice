<?php

namespace App\Libraries;

use Gufy\PdfToHtml\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

require_once (app_path('Sdk/vendor/autoload.php'));

use Gufy\PdfToHtml\Config;

class PdfToHtmlFile {

    private $attachments;

    public function __construct($attachments) {

        $this->attachments = $attachments;
    }

    public function convertPdfToHtml() {

        if (env('APP_ENV') == 'local') {
//             For Window
            Config::set('pdftohtml.bin', public_path('poppler-0.68.0/bin/pdftohtml.exe'));
            Config::set('pdfinfo.bin', public_path('poppler-0.68.0/bin/pdfinfo.exe'));
        }


        if (isset($this->attachments['is_attachment']) && $this->attachments['is_attachment'] == 1) {
            $fileName = 'inv-' . Str::random(5) . '-' . time();
            Storage::put('invoice/pdf/' . $fileName . '.pdf', $this->attachments['attachment']);

            $pdf_saved_file = public_path('storage/invoice/pdf/' . $fileName . '.pdf');

            $pdf = new Pdf($pdf_saved_file);
            $html = $pdf->html();

            Storage::put('invoice/html/' . $fileName . '.html', $html);
            $htmlPath = 'storage/invoice/html/' . $fileName . '.html';
            $pdfPath = 'storage/invoice/pdf/' . $fileName . '.pdf';
            $data = array('filePath' => $pdfPath, 'htmlPath' => $htmlPath);
        } else {
            $fileName = 'inv-' . Str::random(5) . '-' . time();
            Storage::putFileAs('invoice/pdf', $this->attachments['attachment'], $fileName . '.pdf');
            $pdf_saved_file = public_path('storage/invoice/pdf/' . $fileName . '.pdf');

            $pdf = new Pdf($pdf_saved_file);
            $html = $pdf->html();

            Storage::put('invoice/html/' . $fileName . '.html', $html);
            $htmlPath = 'storage/invoice/html/' . $fileName . '.html';
            $pdfPath = 'storage/invoice/pdf/' . $fileName . '.pdf';
            $htmlData = file_get_contents($htmlPath);

            $data = array('filePath' => $pdfPath, 'htmlPath' => $htmlPath, 'htmlData' => $html);
        }

        if (env('APP_ENV') != 'local') {
            // For Ubuntu
            \Gufy\PdfToHtml\Config::set('pdftohtml.bin', '/usr/local/bin/pdftohtml');
            // change pdfinfo bin location
            \Gufy\PdfToHtml\Config::set('pdfinfo.bin', '/usr/local/bin/pdfinfo');
        }

        return $data;
    }

}
