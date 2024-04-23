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


class ParserBilag 
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
            $invoiceData['invoice_number']    = $DOM->getElementsByTagName('p')->item(9)->nodeValue;
            $invoiceData['invoice_date']      = str_replace(['Â','Ã','Ã'], ['','','Ø'], $DOM->getElementsByTagName('p')->item(11)->nodeValue);
            $invoiceData['cvr_number']        = $DOM->getElementsByTagName('p')->item(15)->nodeValue;

            $invoiceData['total']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 21)->nodeValue);
            $invoiceData['sub_total']   = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 21)->nodeValue);
            $invoiceData['vat']         = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($length - 6)->nodeValue);
            $invoiceData['grand_total'] = str_replace(['.',',','Â DKK'], ['','.',''], $DOM->getElementsByTagName('p')->item($length - 17)->nodeValue);

            $shipping_address = $DOM->getElementsByTagName('p')->item(7)->nodeValue;
            $billing_address  = $DOM->getElementsByTagName('p')->item(1)->nodeValue;
            $billing_address = str_replace('Leveringsadresse', '', $billing_address);

            $invoiceData['billing_address']   = str_replace(['Â','Ã','Ã'], ['','','Ø'], $shipping_address);
            $invoiceData['shipping_address']  = str_replace(['Â','Ã','Ã'], ['','','Ø'], $billing_address);
            // $invoiceData['vat_amount']         = $DOM->getElementsByTagName('p')->item($length - 5)->nodeValue;

            $invoice        = Invoice::create($invoiceData);
            $invoice->slug  = Str::slug($invoice->title."-".$invoice->id);
            $invoice->save();

            // Add user vendor
            $results = DB::select('select id FROM user_vendors where user_id = "'.$invoice->user_id.'" AND vendor_id = "'.$this->vendor->id.'" ');
            if(count($results) == 0){
                DB::insert('insert into user_vendors (user_id, vendor_id) values ('.$invoice->user_id.', '.$this->vendor->id.')');
            }
    
            //Product List
            $index = 0;
            for($i = 28; $i < $length; $i++){

                $name = $DOM->getElementsByTagName('p')->item($i++)->nodeValue;
                $pos = 0;
                preg_match('/[a-z]/i', $name, $m, PREG_OFFSET_CAPTURE);

                if (sizeof($m)){
                    $pos = $m[0][1];
                }

                $productList[$index]['status']       = 1;
                $productList[$index]['slug']         = 'EMPTY-SLUG';
                $productList[$index]['user_id']      = $this->user->id;
                $productList[$index]['invoice_id']   = $invoice->id;
                $productList[$index]['vendor_id']    = $this->vendor->id;

                $productList[$index]['product_no']   = substr($name, 0, $pos);
                $productList[$index]['name']         = str_replace(['Â','Ã','Ã'], ['','','Ø'], substr($name, $pos, strlen($name)));

                $check   = $DOM->getElementsByTagName('p')->item($i)->nodeValue;

                if(preg_match('(stk/pk|stk|Stk|rl/krt)', $check) === 0){
                    $i++;
                }
                $i++;

                $productList[$index]['qty']         = str_replace(['.',',','Â Pk'], ['','.',''], $DOM->getElementsByTagName('p')->item($i++)->nodeValue);
                $productList[$index]['price']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($i++)->nodeValue);
                $productList[$index]['total']       = str_replace(['.',','], ['','.'], $DOM->getElementsByTagName('p')->item($i)->nodeValue);
                $productList[$index]['sub_total']   = $productList[$index]['total'];
                $productList[$index]['grand_total'] = $productList[$index]['total'];
        
                $check = $DOM->getElementsByTagName('p')->item($i+1)->nodeValue;
                $check = explode(" ", $check);
                $check = $check[0];

                if($check == 'SamletÂ'){
                    break;
                }
                $index++;
            }

            # Add Invoice with products
            foreach($productList as $pv) {
                
                $product        = Product::create($pv);
                $product->slug  = Str::slug($product->name."-".$product->id);
                $product->save();
            }
        }
    }   
}