<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory as phpSpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;


class ExcelToHtmlFile
{
    private $attachments;
    public function __construct($attachments){

        $this->attachments = $attachments;
    }

    public function convertExcelToHtml(){

        $data = [];

        if (isset($this->attachments['is_attachment']) && $this->attachments['is_attachment'] == 1) {
            $fileName = 'inv-' . Str::random(5) . '-' . time();
            $extension = strtolower(pathinfo($this->attachments['filename'], PATHINFO_EXTENSION));
            Storage::put('invoice/excel/' . $fileName . '.' . $extension, $this->attachments['attachment']);
            $savedExcelFile = public_path('storage/invoice/excel/' . $fileName . '.' . $extension);
            $inputFileType = $extension;

        }
        else{
            $fileName = 'inv-' . Str::random(5) . '-' . time();
            $extension = $this->attachments['attachment']->getClientOriginalExtension();
             Storage::putFileAs('invoice/excel/', $this->attachments['attachment'], $fileName . '.' . $extension);
            $savedExcelFile = public_path('storage/invoice/excel/' . $fileName . '.' . $extension);
            $inputFileType = phpSpreadsheetIOFactory::identify($this->attachments['attachment']);

        }
        if ($inputFileType == 'xlsx')
            $inputFileType = 'Xlsx';

        elseif($inputFileType == 'xls')
            $inputFileType = 'Xls';


            $reader = phpSpreadsheetIOFactory::createReader($inputFileType);
            $phpSpreadsheet = $reader->load($savedExcelFile);

            $htmlWriter = new Html($phpSpreadsheet);

            $tempHtmlFilePath = 'public/invoice/temp/' . time() . '_temp.html';
            Storage::disk('local')->put($tempHtmlFilePath, '');
            $path =  Storage::disk('local')->path($tempHtmlFilePath);

            $htmlWriter->save($path);
            $content = file_get_contents($path);
            $content = preg_replace("/<img[^>]+\>/i", "(image) ", $content);
            $content = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $content);
            Storage::disk('local')->put('public/invoice/html/' . $fileName . '.' . 'html', $content);
            Storage::disk('local')->delete($tempHtmlFilePath);

            $htmlPath = 'storage/invoice/html/' . $fileName . '.html';
            $filePath = 'storage/invoice/excel/' . $fileName . '.' . $extension;
            $data = array('filePath' => $filePath, 'htmlPath' => $htmlPath,'htmlData' => $content);

            return $data;
    }
}
