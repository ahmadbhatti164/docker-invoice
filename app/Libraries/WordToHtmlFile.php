<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory as phpWordIOFactory;


class WordToHtmlFile
{
    private $attachments;
    public function __construct($attachments){

        $this->attachments = $attachments;
    }

    public function convertWordToHtml()
    {

            if (isset($this->attachments['is_attachment']) && $this->attachments['is_attachment'] == 1) {
                $fileName = 'inv-' . Str::random(5) . '-' . time();
                $extension = strtolower(pathinfo($this->attachments['filename'], PATHINFO_EXTENSION));
                Storage::put('invoice/word/' . $fileName . '.' . $extension, $this->attachments['attachment']);
                $savedWordFile = public_path('storage/invoice/word/' . $fileName . '.' . $extension);

            }
            else{
                $fileName = 'inv-' . Str::random(5) . '-' . time();
                $extension = $this->attachments['attachment']->getClientOriginalExtension();
                Storage::putFileAs('invoice/word/', $this->attachments['attachment'], $fileName . '.' . $extension);
                $savedWordFile = public_path('storage/invoice/word/' . $fileName . '.' . $extension);

            }

            $reader = 'MsDoc';
            if ($extension == 'docx') {
                $reader = 'Word2007';
            }
            $phpWord = phpWordIOFactory::createReader($reader)->load($savedWordFile);
            $objWriter = phpWordIOFactory::createWriter($phpWord, 'HTML');


            $tempHtmlFilePath = 'public/invoice/temp/' . $fileName . '_temp.html';
            Storage::disk('local')->put($tempHtmlFilePath,'');

            $path = Storage::disk('local')->path($tempHtmlFilePath);
            $objWriter->save($path);
            $content = file_get_contents($path);
            $content = preg_replace("/<img[^>]+\>/i", "(image) ", $content);
            $content = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $content);
            Storage::disk('local')->put('public/invoice/html/' . $fileName . '.' . 'html', $content);
            Storage::disk('local')->delete($tempHtmlFilePath);

            $htmlPath = 'storage/invoice/html/' . $fileName . '.html';
            $filePath = 'storage/invoice/word/' . $fileName . '.' . $extension;
             return array('filePath' => $filePath, 'htmlPath' => $htmlPath,'htmlData' => $content);


    }

}
