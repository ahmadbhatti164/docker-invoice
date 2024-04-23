<?php

namespace App\Libraries\Parser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Product;

class ParserMBF 
{
    private $user;
    private $vendor;
    private $emailSubject;
    private $file;

    public function __construct($user, $vendor, $emailSubject, $file){
        $this->user = $user;
        $this->vendor = $vendor;
        $this->emailSubject = $emailSubject;
        $this->file = $file;
    }

    public function parse()
    {
        foreach ($this->file as $file) {

            $html = file_get_contents(public_path('storage/invoice/html/'.$file.'.html'));

            $DOM = new \DOMDocument();
            $DOM->loadHTML($html);
            
            $items = $DOM->getElementsByTagName('p');
            $length = $items->length;

            //Saving Invoice
            $invoiceData['user_id']           = $this->user->id;
            $invoiceData['vendor_id']         = $this->vendor->id;
            $invoiceData['slug']              = 'EMPTY-SLUG';
            $invoiceData['pdf_file']          = 'storage/invoice/pdf/'.$file.'.pdf';
            $invoiceData['html_file']         = 'storage/invoice/html/'.$file.'.html';
            
            $invoiceData['status']          = 1;
            $invoiceData['title']           = $this->emailSubject;
            $invoiceData['invoice_number']  = $DOM->getElementsByTagName('p')->item(24)->nodeValue;
            $invoiceData['cvr_number']      = $DOM->getElementsByTagName('p')->item(73)->nodeValue;
            $invoiceData['invoice_date']    = $DOM->getElementsByTagName('p')->item(37)->nodeValue;
            $invoiceData['total']           = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 11)->nodeValue);
            $invoiceData['sub_total']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 11)->nodeValue);
            $invoiceData['vat']             = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 9)->nodeValue);
            $invoiceData['grand_total']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 6)->nodeValue);
            $invoiceData['billing_address']   = str_replace(['Â','Ã','Ã'], ['','','Ø'], $DOM->getElementsByTagName('p')->item(13)->nodeValue);
            $invoiceData['shipping_address']  = str_replace(['Â','Ã','Ã'], ['','','Ø'], $DOM->getElementsByTagName('p')->item(14)->nodeValue);
            // $invoiceData['vatBasis']    = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 10)->nodeValue);
            // $invoiceData['vatAmount']   = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 8)->nodeValue);
            // $invoiceData['val']         = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 7)->nodeValue);

            $balance   = $DOM->getElementsByTagName('p')->item($length - 4);
            $balance   = $balance->nodeValue;
            $balance   = str_replace("DeresÂ gamleÂ saldoÂ er..............:Â Â ","",$balance);
            $balance   = str_replace("0SaldoÂ erÂ (inklÂ denneÂ faktura):Â Â "," ",$balance);
            $balance   = explode(" ", $balance);
            
            $dateTemp   = explode("/", $invoiceData['invoice_date']);
            $day        = $dateTemp[0];
            $dateTemp   = explode("-", $dateTemp[1]);
            $month      = $dateTemp[0];
            $year       = '20'.$dateTemp[1];
            $invoiceData['invoice_date'] = $year.'-'.$month.'-'.$day;

            // $invoiceData['old_balance']  = str_replace(['.',','], ['','.'], $balance[0]);
            $invoiceData['balance']      = str_replace(['.',','], ['','.'], $balance[1]);

            $invoice        = Invoice::create($invoiceData);
            $invoice->slug  = Str::slug($invoice->title."-".$invoice->id);
            $invoice->save();

            // Add user vendor
            $results = DB::select('select id FROM user_vendors where user_id = "'.$invoice->user_id.'" AND vendor_id = "'.$this->vendor->id.'" ');
            if(count($results) == 0){
                DB::insert('insert into user_vendors (user_id, vendor_id) values ('.$invoice->user_id.', '.$this->vendor->id.')');
            }
            
        }
    
        //Product List

        $productList['status']       = 1;
        $productList['slug']         = 'EMPTY-SLUG';
        $productList['user_id']      = $this->user->id;
        $productList['invoice_id']   = $invoice->id;
        $productList['vendor_id']    = $this->vendor->id;

        $productList['product_no']  = $DOM->getElementsByTagName('p')->item(147)->nodeValue;
        $productList['name']        = str_replace(['Â','Ã','Ã'], ['','','Ø'], $DOM->getElementsByTagName('p')->item(148)->nodeValue);
        $productList['qty']         = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(151)->nodeValue);
        $productList['price']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(152)->nodeValue);
        $productList['total']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(153)->nodeValue);
        $productList['sub_total']   = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(153)->nodeValue);
        $productList['grand_total'] = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(153)->nodeValue);
        // $productList['delivery']    = $DOM->getElementsByTagName('p')->item(146)->nodeValue;
        // $productList['type']        = $DOM->getElementsByTagName('p')->item(149)->nodeValue;
        // $productList['batch']       = $DOM->getElementsByTagName('p')->item(150)->nodeValue;

        // $dateTemp   = explode("/", $productList['delivery']);
        // $day        = $dateTemp[0];
        // $dateTemp   = explode("-", $dateTemp[1]);
        // $month      = $dateTemp[0];
        // $year       = '20'.$dateTemp[1];
        // $productList['delivery'] = $year.'-'.$month.'-'.$day;

        # Add Invoice with products
        $product        = Product::create($productList);
        $product->slug  = Str::slug($product->name."-".$product->id);
        $product->save();
        
    }   
}