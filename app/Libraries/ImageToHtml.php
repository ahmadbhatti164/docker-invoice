<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;


class ImageToHtml
{
    private $attachments;
    public function __construct($attachments){

        $this->attachments = $attachments;
    }

    public function convertImageToHtml()
    {
        $data = [];
        if (isset($this->attachments['is_attachment']) && $this->attachments['is_attachment'] == 1) {
            $fileName = 'inv-' . Str::random(5) . '-' . time();
            $extension = strtolower(pathinfo($this->attachments['filename'], PATHINFO_EXTENSION));
            Storage::put('invoice/image/' . $fileName . '.' . $extension, $this->attachments['attachment']);
            $savedImageFile = public_path('storage/invoice/image/' . $fileName . '.' . $extension);

        }
        else{
            $fileName = 'inv-' . Str::random(5) . '-' . time();
            $extension = $this->attachments['attachment']->getClientOriginalExtension();
            Storage::putFileAs('invoice/image/', $this->attachments['attachment'], $fileName . '.' . $extension);
            $savedImageFile = public_path('storage/invoice/image/' . $fileName . '.' . $extension);

        }

        $ocr = new TesseractOCR();
        $ocr->image($savedImageFile);
        $content = $ocr->lang('eng', 'jpn', 'spa')
            ->psm(6)
            ->run();

        Storage::disk('local')->put('public/invoice/html/' . $fileName . '.' . 'html', $content);

        $htmlPath = 'storage/invoice/html/' . $fileName . '.html';
        $filePath = 'storage/invoice/image/' . $fileName . '.' . $extension;
        $data = array('filePath'=>$filePath,'htmlPath'=>$htmlPath,'htmlData'=>$content);
        return $data;
    }

}
