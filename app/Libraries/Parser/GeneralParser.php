<?php

namespace App\Libraries\Parser;

use App\Services\CreateCompleteInvoiceService;
use Carbon\Carbon;
use stringEncode\Exception;
use Throwable;

class GeneralParser
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

    /**
     * @param $parser_parameter
     * @param $length
     */
    public function stringReplace($parser_parameter,$length): array
    {

        $array = explode("@", $parser_parameter);
        $i = 0;
        $replaceTo = $replaceFrom = [];
        foreach ($array as $arr) {
            if($i == 0){
                if (strstr($arr, '-')) {
                    $total = explode("-", $arr);
                    $parser_parameter = $length - (int)$total[1];
                }
                else
                    $parser_parameter = (int)$arr;
            }
            else {
                $replaceFrom[] = (string)$arr;
                $replaceTo[] = '';
            }
            $i++;
        }
        return array($replaceFrom,$replaceTo,$parser_parameter);
    }

    public function parse(): bool
    {
        $parser_parameters = json_decode($this->vendor->parser_parameters, true);

        try {
            foreach ($this->file as $file) {

                $htmlFile = file((asset($file['htmlPath'])));

                $length = count($htmlFile);

                //Saving Invoice
                $invoiceData['user_id'] = $this->user->id;
                $invoiceData['vendor_id'] = $this->vendor->id;
                $invoiceData['slug'] = 'EMPTY-SLUG';
                $invoiceData['pdf_file'] = asset($file['filePath']);
                $invoiceData['html_file'] = asset($file['htmlPath']);

                $invoiceData['status'] = 1;
                $invoiceData['title'] = $this->emailSubject;

                // invoice number
                if(strstr($parser_parameters['invoice_no'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['invoice_no'],$length);
                    $invoiceData['invoice_number'] = str_replace($replaceFrom, $replaceTo,$htmlFile[$pItem]);
                }elseif (strstr($parser_parameters['invoice_no'], '-'))
                {
                    $total = explode("-", $parser_parameters['invoice_no']);
                    $parser_parameters['invoice_no'] = $length - $total[1];
                    $invoiceData['invoice_number'] = $htmlFile[$parser_parameters['invoice_no']];
                }
                else
                    $invoiceData['invoice_number'] = $htmlFile[$parser_parameters['invoice_no']];

                $invoiceData['invoice_number'] = strip_tags($invoiceData['invoice_number']);
                $invoiceData['invoice_number'] = str_replace(["\r\n","\r","\n"], "", $invoiceData['invoice_number']);

                // CVR no
                if(strstr($parser_parameters['cvr_number'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['cvr_number'],$length);
                    $invoiceData['cvr_number'] = str_replace($replaceFrom, $replaceTo,$htmlFile[$pItem]);
                }elseif (strstr($parser_parameters['cvr_number'], '-'))
                {
                    $total = explode("-", $parser_parameters['cvr_number']);
                    $parser_parameters['cvr_number'] = $length - $total[1];
                    $invoiceData['cvr_number'] = $htmlFile[$parser_parameters['cvr_number']];
                }
                else
                    $invoiceData['cvr_number'] = $htmlFile[$parser_parameters['cvr_number']];

                $invoiceData['cvr_number'] = strip_tags($invoiceData['cvr_number']);
                $invoiceData['cvr_number'] = str_replace(["\r\n","\r","\n"], "", $invoiceData['cvr_number']);


                // Total
                if(strstr($parser_parameters['total'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['total'],$length);
                    $invoiceData['total'] = str_replace($replaceFrom, $replaceTo,$htmlFile[$pItem]);
                    $invoiceData['total'] = str_replace(['.',','], ['','.'], $invoiceData['total']);
                }elseif (strstr($parser_parameters['total'], '-'))
                {
                    $total = explode("-", $parser_parameters['total']);
                    $parser_parameters['total'] = $length - $total[1];
                    $invoiceData['total'] = $htmlFile[$parser_parameters['total']];
                    $invoiceData['total'] = str_replace(['.',','], ['','.'], $invoiceData['total']);
                }
                else
                    $invoiceData['total'] = str_replace(['.', ',','Â DKK',' '], ['', '.', '',''], $htmlFile[$parser_parameters['total']]);

                $invoiceData['total'] = strip_tags($invoiceData['total']);
                $invoiceData['total'] = floatval(str_replace(["\r\n","\r","\n"], "", $invoiceData['total']));

                // Vat
                if(strstr($parser_parameters['vat'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['vat'],$length);
                    $invoiceData['vat'] = str_replace($replaceFrom, $replaceTo,$htmlFile[$pItem]);
                    $invoiceData['vat'] = str_replace(['.',','], ['','.'], $invoiceData['vat']);
                }
                elseif (strstr($parser_parameters['vat'], '-')) {
                    $vat = explode("-", $parser_parameters['vat']);
                    $parser_parameters['vat'] = $length - $vat[1];
                    $invoiceData['vat'] = $htmlFile[$parser_parameters['vat']];
                    $invoiceData['vat'] = str_replace(['.',','], ['','.'], $invoiceData['vat']);
                }
                else
                    $invoiceData['vat'] = str_replace(['.', ',',' '], ['', '.',''], $htmlFile[$parser_parameters['vat']]);

                $invoiceData['vat'] = strip_tags($invoiceData['vat']);
                $invoiceData['vat'] = floatval(str_replace(["\r\n","\r","\n"], "", $invoiceData['vat']));



                // Sub Total
                if(strstr($parser_parameters['sub_total'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['sub_total'],$length);
                    $invoiceData['sub_total'] = str_replace($replaceFrom,$replaceTo, $htmlFile[$pItem]);
                    $invoiceData['sub_total'] = floatval(str_replace(['.',','], ['','.'], $invoiceData['sub_total']));
                }
                elseif (strstr($parser_parameters['sub_total'], '-')) {
                    $sub_total = explode("-", $parser_parameters['sub_total']);
                    $parser_parameters['sub_total'] = $length - $sub_total[1];
                    $invoiceData['sub_total'] = $htmlFile[$parser_parameters['sub_total']];
                    $invoiceData['sub_total'] = str_replace(['.',','], ['','.'], $invoiceData['sub_total']);
                }
                else
                    $invoiceData['sub_total'] = str_replace(['.', ',','Â DKK',' '], ['', '.', '',''], $htmlFile[$parser_parameters['sub_total']]);

                $invoiceData['sub_total'] = strip_tags($invoiceData['sub_total']);
                $invoiceData['sub_total'] = floatval(str_replace(["\r\n","\r","\n"], "", $invoiceData['sub_total']));


                // Grand Total
                if(strstr($parser_parameters['grand_total'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['grand_total'],$length);
                    $invoiceData['grand_total'] = str_replace($replaceFrom,$replaceTo, $htmlFile[$pItem]);
                }elseif (strstr($parser_parameters['grand_total'], '-'))
                {
                    $total = explode("-", $parser_parameters['grand_total']);
                    $parser_parameters['grand_total'] = $length - $total[1];
                    $invoiceData['grand_total'] = $htmlFile[$parser_parameters['grand_total']];
                }
                else
                    $invoiceData['grand_total'] = $htmlFile[$parser_parameters['grand_total']];


                $invoiceData['grand_total'] = str_replace(['.', ',','Â DKK',' '], ['', '.', ''.''], $invoiceData['grand_total']);
                $invoiceData['grand_total'] = strip_tags($invoiceData['grand_total']);
                $invoiceData['grand_total'] = floatval(str_replace(["\r\n","\r","\n"], "", $invoiceData['grand_total']));


                $invoiceData['vat'] = round(($invoiceData['vat'] / $invoiceData['total']) * 100, 2); //percentage

                if(isset($parser_parameters['billing_address']))
                    $invoiceData['billing_address'] = str_replace(['Â', 'Ã', 'Ã', 'Yasar Nazir','\r\n','\r','\n'], ['', '', 'Ø', '', '', '', ''], strip_tags($htmlFile[$parser_parameters['billing_address']]));

                if(isset($parser_parameters['shipping_address']))
                    $invoiceData['shipping_address'] = str_replace(['Â', 'Ã', 'Ã', 'Yasar Nazir','\r\n','\r','\n'], ['', '', 'Ø', '', '', '', ''], strip_tags($htmlFile[$parser_parameters['shipping_address']]));

                //------------ MBF
                if (isset($parser_parameters['balance'])) {
                    if (strstr($parser_parameters['balance'], '-')) {
                        $balance = explode("-", $parser_parameters['balance']);
                        $parser_parameters['balance'] = $length - $balance[1];
                    }

                    $balance = strip_tags($htmlFile[$parser_parameters['balance']]);
                    $balance = str_replace("DeresÂ gamleÂ saldoÂ er..............:Â Â ", "", $balance);
                    $balance = str_replace("0SaldoÂ erÂ (inklÂ denneÂ faktura):Â Â ", " ", $balance);
                    $balance = explode(" ", $balance);
                    $invoiceData['balance'] = str_replace(['.', ','], ['', '.'], $balance[1]);
                }

                // Invoice Date
                if(strstr($parser_parameters['invoice_date'], '@')) {
                    list($replaceFrom,$replaceTo,$pItem) = $this->stringReplace($parser_parameters['invoice_date'],$length);
                    $invoiceData['invoice_date'] = str_replace($replaceFrom,$replaceTo, $htmlFile[$pItem]);
                }
                elseif (strstr($parser_parameters['invoice_date'], '-')) {
                    $invoice_date = explode("-", $parser_parameters['invoice_date']);
                    $parser_parameters['invoice_date'] = $length - $invoice_date[1];
                    $invoiceData['invoice_date'] = $htmlFile[$parser_parameters['invoice_date']];
                }
                else
                    $invoiceData['invoice_date'] = $htmlFile[$parser_parameters['invoice_date']];

                //$invoiceData['invoice_date'] = str_replace(['.', ',','Â DKK',' '], ['', '.', ''.''], $invoiceData['grand_total']);
                $invoiceData['invoice_date'] = strip_tags($invoiceData['invoice_date']);
                $invoiceData['invoice_date'] = str_replace(["\r\n","\r","\n"], "", $invoiceData['invoice_date']);
                $invoiceData['invoice_date'] = str_replace(["­"], "-", $invoiceData['invoice_date']);


                //dd($invoiceData['invoice_date']);
                if (strstr($invoiceData['invoice_date'], '/') && strstr($invoiceData['invoice_date'], '-')) {
                    $dateTemp = explode("/", $invoiceData['invoice_date']);
                    $day = $dateTemp[0];
                    $dateTemp = explode("-", $dateTemp[1]);
                    $month = $dateTemp[0];
                    $year = '20' . $dateTemp[1];
                    $invoiceData['invoice_date'] = $year . '-' . $month . '-' . $day;
                } elseif (strstr($invoiceData['invoice_date'], 'Â') || strstr($invoiceData['invoice_date'], '-') || strstr($invoiceData['invoice_date'], '­')) {
                    $invoiceData['invoice_date'] = str_replace(['Â', 'Ã', 'Ã', 'Â DKK', '­',' '], ['', '-', 'Ø', '', '-',''], $invoiceData['invoice_date']);
                    $dateTemp = explode("-", $invoiceData['invoice_date']);

                    if (strlen($dateTemp[0]) > 3)
                        $invoiceData['invoice_date'] = $dateTemp[0] . '-' . $dateTemp[1] . '-' . $dateTemp[2];
                    else
                        $invoiceData['invoice_date'] = $dateTemp[2] . '-' . $dateTemp[1] . '-' . $dateTemp[0];
                }
                else
                    $invoiceData['invoice_date'] = date('Y-m-d', strtotime($invoiceData['invoice_date']));


                try {
                    Carbon::parse($invoiceData['invoice_date']);
                } catch (\Exception $e) {
                    $invoiceData['invoice_date'] = Carbon::now()->format('Y-m-d');
                }

                // Product List
                $index = 0;
                $products_length = isset($parser_parameters['products_end']) ? $parser_parameters['products_end'] : $length;
                $products_length = $products_length-1;
                $productTotal = $parser_parameters['products_columns']['product_total'];

                $productStart = $parser_parameters['products_start'];

                for ($i=($productStart );$i<$products_length;$i++) {
                    for ($j = 1; $j <= $productTotal ; $j++) {

                        $k = $i + ($productTotal-1);
                        if ($j == 1 && isset($htmlFile[$k])) {

                            $total = $htmlFile[$k];
                            $total = str_replace(["\r\n","\r","\n"], "", strip_tags($total));
                            $total = floatval(str_replace(['.', ',', 'DKK', 'Â',' '], ['', '.', '', '',''], ($total)));
                            if (!$total && (!is_int($total) || !is_float($total)) ) {
                                break 2;
                            }

                        }
                        if(!isset($htmlFile[$k]))
                            break 2;

                        if ( isset($parser_parameters['products_columns']['product_no']) && $parser_parameters['products_columns']['product_no'] == $j) {

                            $k = $i + $j;
                            $product_no = $htmlFile[$k-1];
                            $product_no = str_replace(["\r\n","\r","\n"], "", strip_tags($product_no));
                            $product_no = str_replace(['*', 'Â '], ['', ''], $product_no);

                            $result = '';
                            foreach (str_split($product_no) as $char) {
                                if (is_numeric($char)) {
                                    $result .= $char;
                                } else {
                                    break;
                                }
                            }
                            $productList[$index]['product_no'] = $result;
                        }
                        if (isset($parser_parameters['products_columns']['name']) && $parser_parameters['products_columns']['name'] == $j) {
                            $k = $i + $j;
                            $productList[$index]['name'] = str_replace(['Â', 'Ã', 'Ã', '*',"\r\n","\r","\n"], ['', '', 'Ø', '','','',''], strip_tags($htmlFile[$k-1]));
                            if (isset($productList[$index]['product_no']) && $productList[$index]['product_no'] == $productList[$index]['name']) {
                                //$i++;
                                $k = $i + $j;
                                $productList[$index]['name'] = str_replace(['Â', 'Ã', 'Ã', '*',"\r\n","\r","\n"], ['', '', 'Ø', '','','',''], strip_tags($htmlFile[$k-1]));
                            }
                        }

                        if (isset($parser_parameters['products_columns']['stock']) && $parser_parameters['products_columns']['stock'] == $j) {
                            $k = $i + $j;
                            $productList[$index]['stock'] = str_replace(['.', ',', 'Â '], ['', '.', ''], strip_tags($htmlFile[$k-1]));
                            $stk = $productList[$index]['stock'];
                            if (strpos($stk, 'Stk') !== false || strpos($stk, 'stk') !== false || strpos($stk, 'krt') !== false) {
                                $productList[$index]['stock']  = $htmlFile[$k-1];
                            }
                            else
                            {
                                $productList[$index]['name'] = $productList[$index]['name'].str_replace(['Â', 'Ã', 'Ã'], ['', '', 'Ø'], $stk);
                                $i++;
                                $k = $i + $j;
                                $productList[$index]['stock']  = $htmlFile[$k-1];
                            }

                            $productList[$index]['stock']  = str_replace(['.',',','Â ',"\r\n","\r","\n"], ['','.','','','',''], strip_tags($productList[$index]['stock']));

                        }

                        if (isset($parser_parameters['products_columns']['qty']) && $parser_parameters['products_columns']['qty'] == $j) {
                            $k = $i + $j;
                            $productList[$index]['qty'] = str_replace(['.', ',', 'Â ',"\r\n","\r","\n"], ['', '.', '','','',''], strip_tags($htmlFile[$k-1]));

                            if( isset($productList[$index]['stock']) && 1 !== preg_match('~[0-9]~', $productList[$index]['stock']))
                                $productList[$index]['qty'] =  $productList[$index]['qty'] .' '. $productList[$index]['stock'];

                            /*  $qty = $productList[$index]['qty'];
                             if (strpos($qty, 'Stk') !== false || strpos($qty, 'stk') !== false || strpos($qty, 'krt') !== false) {
                                 $productList[$index]['Stk']  = str_replace(['.',',','Â '], ['','.',''], $DOM->getElementsByTagName('p')->item($k)->nodeValue);
                             }
                             else
                             {
                                 $productList[$index]['name'] = $productList[$index]['name'].str_replace(['Â', 'Ã', 'Ã'], ['', '', 'Ø'], $qty);
                                 $i++;
                                 $k = $i + $j;
                                 $productList[$index]['Stk']  = str_replace(['.',',','Â '], ['','.',''], $DOM->getElementsByTagName('p')->item($k)->nodeValue);
                             }*/
//                        if(1 !== preg_match('~[0-9]~', $productList[$index]['qty'])){
//                            $i++;
//                            $k = $i + $j;
//                            $productList[$index]['qty']  = str_replace(['.',',','Â '], ['','.',''], $DOM->getElementsByTagName('p')->item($k)->nodeValue);
//                        }

                        }

                        if (isset($parser_parameters['products_columns']['price']) && $parser_parameters['products_columns']['price'] == $j) {
                            $k = $i + $j;
                            $productList[$index]['price'] = floatval(str_replace(['.', ',',' ',"\r\n","\r","\n"], ['', '.',''], strip_tags($htmlFile[$k-1])));
                            //$productList[$index]['product_total'] = str_replace(['.', ','], ['', '.'], $DOM->getElementsByTagName('p')->item(++$k)->nodeValue);
                        }
                        if (isset($parser_parameters['products_columns']['product_total']) && $parser_parameters['products_columns']['product_total'] == $j) {
                            $k = $i + $j;
                            $productList[$index]['total'] = floatval(str_replace(['.', ',','DKK','Â',' '], ['', '.','','',''], strip_tags($htmlFile[$k-1])));
                        }
                    }

                    if (isset($productList[$index]['total'])) {
                        $productList[$index]['user_id'] = $this->user->id;
                        $productList[$index]['vendor_id'] = $this->vendor->id;
                        $productList[$index]['sub_total'] = $productList[$index]['total'];
                        $productList[$index]['grand_total'] = $productList[$index]['total'];
                    }
                    $i = $i + ($productTotal-1);
                    $index++;
                }

                $createService = new CreateCompleteInvoiceService($invoiceData,$this->vendor,$productList);
                $createInvoice = $createService->create();

                if($createInvoice == False)
                    Throw new Exception();
            }

            return true;
        }catch (Throwable $e) {
            dd($e);
            return false;
        }
    }
}
