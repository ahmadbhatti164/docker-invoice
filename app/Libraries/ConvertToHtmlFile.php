<?php


namespace App\Libraries;


use Illuminate\Support\Facades\Log;

class ConvertToHtmlFile
{
    private $attachments;
    public function __construct($attachments){

        $this->attachments = $attachments;
    }

    public function convert()
    {
        Log::info('files',$this->attachments);

        $files = [];
        foreach ($this->attachments as $attachment){
            if ($attachment['is_attachment'] == 1) {
                //Log::info('files', $attachment);
               // Log::info('ext', ['extension'=>pathinfo($attachment['filename'],PATHINFO_EXTENSION)]);
                $extension = strtolower(pathinfo($attachment['filename'],PATHINFO_EXTENSION));
                //dd($extension);
                if ($extension == 'pdf' || $extension == 'PDF') {
                    $pdfToHtml = new PdfToHtmlFile($attachment);
                    $files[] = $pdfToHtml->convertPdfToHtml();
                } elseif ($extension == 'xls' || $extension == 'xlsx') {
                    $excelToHtml = new ExcelToHtmlFile($attachment);
                    $files[] = $excelToHtml->convertExcelToHtml();
                } elseif ($extension == 'doc' || $extension == 'docx') {
                    $wordToHtml = new WordToHtmlFile($attachment);
                    $files[] = $wordToHtml->convertWordToHtml();
                }elseif ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                    $imageToHtml = new ImageToHtml($attachment);
                    $files[] = $imageToHtml->convertImageToHtml();
                }
            }
        }
        return $files;
    }
}
