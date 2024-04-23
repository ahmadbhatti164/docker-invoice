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

class ParserDFS
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

            $invoiceData['status']            = 1;
            $invoiceData['title']             = $this->emailSubject;
            $invoiceData['invoice_number']    = $DOM->getElementsByTagName('p')->item(46)->nodeValue;
            $invoiceData['cvr_number']        = $DOM->getElementsByTagName('p')->item(26)->nodeValue;
            $invoiceData['invoice_date']      = date('Y-m-d', strtotime($DOM->getElementsByTagName('p')->item(13)->nodeValue));
            $invoiceData['total']             = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(15)->nodeValue);
            $invoiceData['vat']               = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(16)->nodeValue);
            $invoiceData['sub_total']         = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(15)->nodeValue);
            $invoiceData['grand_total']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(17)->nodeValue);

            $address                          = str_replace(['Â','Ã','Ã','Yasar Nazir'], ['','','Ø',''], $DOM->getElementsByTagName('p')->item(12)->nodeValue);
            $invoiceData['vat']               = round(($invoiceData['vat'] / $invoiceData['total']) * 100 ,2); //percentage
            $invoiceData['billing_address']   = $address;
            $invoiceData['shipping_address']  = $address;
            // $invoiceData['interest_rate']     = $DOM->getElementsByTagName('p')->item(39)->nodeValue;
            
            $invoice        = Invoice::create($invoiceData);
            $invoice->slug  = Str::slug($invoice->title."-".$invoice->id);
            $invoice->save();
            
            // Add user vendor
            $results = DB::select('select id FROM user_vendors where user_id = "'.$invoice->user_id.'" AND vendor_id = "'.$this->vendor->id.'" ');
            if(count($results) == 0){
                DB::insert('insert into user_vendors (user_id, vendor_id) values ('.$invoice->user_id.', '.$this->vendor->id.')');
            }

            // Product List
            $index = 0;
            for($i = 51; $i < $length; $i++){

                $productList[$index]['status']       = 1;
                $productList[$index]['slug']         = 'EMPTY-SLUG';
                $productList[$index]['user_id']      = $this->user->id;
                $productList[$index]['invoice_id']   = $invoice->id;
                $productList[$index]['vendor_id']    = $this->vendor->id;

                $productList[$index]['product_no']   = $DOM->getElementsByTagName('p')->item($i)->nodeValue;
                $productList[$index]['name']         = str_replace(['Â','Ã','Ã'], ['','','Ø'], $DOM->getElementsByTagName('p')->item(++$i)->nodeValue);
                $productList[$index]['stock']        = $DOM->getElementsByTagName('p')->item(++$i)->nodeValue;
                $productList[$index]['qty']          = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(++$i)->nodeValue);
                $productList[$index]['price']        = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(++$i)->nodeValue);
                $productList[$index]['total']        = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item(++$i)->nodeValue);
                $productList[$index]['sub_total']    = $productList[$index]['total']; 
                $productList[$index]['grand_total']  = $productList[$index]['total'];
                $index++;
            }
        }

        # Add Invoice with products
        foreach ($productList as $pv) {
            
            $product        = Product::create($pv);
            $product->slug  = Str::slug($product->name."-".$product->id);
            $product->save();
        }
    }   
}