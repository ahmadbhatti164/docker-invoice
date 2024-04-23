<?php

namespace App\Http\Controllers\Admin\OCR;

use App\Http\Controllers\Controller;
use App\Models\OCR;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpWord\IOFactory as phpWordIOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as phpSpreadsheeetIOFactory;

class OCRController extends Controller
{
    public function index(Request $request)
    {
        $user = User::select('id','name','email')->where('email', 'invoice-testing@sixlogics.com')
            ->where('status', 1)->first();

        $vendor = Vendor::select('id','name','email','parser','parser_parameters')->where('email', 'imageocr@gmail.com')
            ->where('status', 1)->first();

        $files = [''];
        $parserClass = 'App\Libraries\Parser\GeneralParserOCR';
        $parser = new $parserClass($user, $vendor, 'data', $files);
        $parser = $parser->parse();
        dd($parser);

        if ($request->ajax()){
            $ocrs = OCR::with('user','vendor');
            return DataTables::eloquent($ocrs)->toJson();
        }
        return view('admin.ocr.index');
    }

    public function create()
    {
        return view('admin.ocr.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ocr_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10000',
        ]);
//        $path = '';
//        $extension = $request->file('ocr_file')->getClientOriginalExtension();
//        if($extension=='pdf'){
//            $path = Storage::putFile('invoice/pdf', $request->file('ocr_file'));
//            Ghostscript::setGsPath('C:\Program Files\gs\gs9.53.3\bin\gswin64.exe');
//            $pdf = new Pdf(public_path('storage/'.$path));
//            $pdf->setOutputFormat('jpg')->saveImage(asset('storage/invoice/images/'.time()));
//            dd($pdf);
//
//        }
//        else{
//            $path = Storage::putFile('invoice/images', $request->file('ocr_file'));
//        }
        $path = Storage::putFile('invoice/images', $request->file('ocr_file'));
        $ocr = new TesseractOCR();
        $ocr->image(public_path('storage/'.$path));
        $content = $ocr->lang('eng', 'jpn', 'spa')
            ->psm(6)
        ->run();

        $myfile = fopen("./finalchange.txt", "w") or die("Unable to open file!");

        fwrite($myfile, $content);

        fclose($myfile);

        dd($content);
        $ocr = new OCR();
        $ocr->content = $content;
        $ocr->url = public_path('storage/'.$path);
        $ocr->created_by = auth()->user()->id;
        $ocr->save();

        return redirect()->route('ocr.index')->with('success','OCR Added Successfully');
    }
    //storeWord
    public function storeWord(Request $request)
    {
        $request->validate([
            'ocr_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx|max:2048',
        ]);

        $extension = $request->file('ocr_file')->getClientOriginalExtension();
        $reader = 'MsDoc';
        if($extension == 'docx'){
            $reader = 'Word2007';
        }
        $phpWord = phpWordIOFactory::createReader($reader)->load($request->ocr_file);
        $objWriter = phpWordIOFactory::createWriter($phpWord, 'HTML');

        $tempHtmlFilePath = 'public/invoice/temp/' . time() . '_temp.html';
        Storage::disk('local')->put($tempHtmlFilePath, '');

        $path =  Storage::disk('local')->path($tempHtmlFilePath);
        $objWriter->save($path);

        $content = file_get_contents($path);
        $content = preg_replace("/<img[^>]+\>/i", "(image) ", $content);
        $content = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $content);
        Storage::disk('local')->put('public/invoice/html/'.time().'.'.'html', $content);
        Storage::disk('local')->delete($tempHtmlFilePath);
        //$content = strip_tags_content($content, '<td>', TRUE);
        dd($content);

        return redirect()->route('ocr.index')->with('success','OCR Added Successfully');
    }
    //storeSpreadsheet
    public function storeSpreadsheet(Request $request)
    {
        $request->validate([
            'ocr_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx|max:2048',
        ]);

        $inputFileType = phpSpreadsheeetIOFactory::identify($request->file('ocr_file'));
        $reader = phpSpreadsheeetIOFactory::createReader($inputFileType);
        $phpSpreadsheet = $reader->load($request->file('ocr_file'));
        //dd($spreadsheet);
        //$phpExcel = IOFactory::createReader($reader)->load($request->ocr_file);
//        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($phpSpreadsheet, 'HTML');
        $htmlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Html($phpSpreadsheet);
        $tempHtmlFilePath = 'public/invoice/temp/' . time() . '_temp.html';
        Storage::disk('local')->put($tempHtmlFilePath, '');
        $path =  Storage::disk('local')->path($tempHtmlFilePath);

        $htmlWriter->save($path);
        $content = file_get_contents($path);
        Storage::disk('local')->put('public/invoice/html/'.time().'.'.'html', $content);
        Storage::disk('local')->delete($tempHtmlFilePath);
        $content = preg_replace("/<img[^>]+\>/i", "(image) ", $content);
        $content = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $content);
        dd($content);

        return redirect()->route('ocr.index')->with('success','OCR Added Successfully');
    }
}
