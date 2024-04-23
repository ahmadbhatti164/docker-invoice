<?php

namespace App\Libraries\Parser;

use App\Services\CreateCompleteInvoiceService;
use Carbon\Carbon;
use stringEncode\Exception;
use Throwable;

class GeneralParserOCR
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

    private function multipleIndexConcat($company_address,$htmlFile,$length){
        $companyAddress = '';
        $total = explode("+", $company_address);
        foreach ($total as $positive){
            if (strstr($positive, '@')) {
                list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($positive, $length);
                $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                $companyAddress .= ' '.str_replace($replaceFrom, $replaceTo,$find);
            }else{
                $companyAddress .= ' '.$this->findDataFromFile($positive, $htmlFile,'value');
            }

        }
        return $companyAddress;
    }
    /**
     * @param $parser_parameter
     * @param $length
     */
    private function stringReplace($parser_parameter,$length): array
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
                    $parser_parameter = $arr;
            }
            else {
                $replaceFrom[] = (string)$arr;
                $replaceTo[] = '';
            }
            $i++;
        }
        return array($replaceFrom,$replaceTo,$parser_parameter);
    }

    /**
     * @param $k
     * @param $htmlFile
     * @return mixed
     */
    private function compareNameWithNextLines($k,$htmlFile): int
    {
        try {
            $currentName = $htmlFile[$k - 1];
            $secondName = $htmlFile[$k];
            $thirdName = $htmlFile[$k + 1];
            $increment = 0;

            if (strpos($currentName, 'left') !== false) {
                $strArray = explode(';', $currentName);

                foreach ($strArray as $str) {
                    if (strpos($str, 'left') !== false) {
                        $size = explode(':', $str);
                        $currentNameSize = str_replace('px', '', ($size[1]));
                    }
                }
            }
            if (strpos($secondName, 'left') !== false) {
                $strArray = explode(';', $secondName);

                foreach ($strArray as $str) {
                    if (strpos($str, 'left') !== false) {
                        $size = explode(':', $str);
                        $secondNameSize = str_replace('px', '', ($size[1]));
                    }
                }
            }
            if (strpos($thirdName, 'left') !== false) {
                $strArray = explode(';', $thirdName);

                foreach ($strArray as $str) {
                    if (strpos($str, 'left') !== false) {
                        $size = explode(':', $str);
                        $thirdNameSize = str_replace('px', '', ($size[1]));
                    }
                }
            }
            if ($currentNameSize == $secondNameSize)
                $increment = 1;
            if ($currentNameSize == $secondNameSize && $currentNameSize == $thirdNameSize)
                $increment = 2;


        }catch (Throwable $e) {
            return $increment = 0;
        }
        return $increment;
    }

    private function findDataFromFile($parser_parameters,$file,$type,$explode=null) //  value or key
    {
       if(!Empty($parser_parameters)) {
           if (is_numeric($parser_parameters)) {
               if ($type == 'value')
                   return $file[$parser_parameters];
               else
                   return $parser_parameters;
           } else {
               if ($explode == null)
                   return $this->getKeyOrValue($parser_parameters, $file, $type);
               else {
                   $val = '';
                   $parser_parameters = explode('|', $parser_parameters);
                   foreach ($parser_parameters as $par)
                   {
                       //echo $par;
                       $val = $this->getKeyOrValue($par, $file, $type);
                        if($val !== null)
                            return $val;
                   }
                   return $val;
               }
           }
       }

    }

    private function getKeyOrValue($par,$file,$type){
        foreach ($file as $key => $value) {
            if (strstr($value, $par)) {
                if ($type == 'value')
                    return $value;
                else
                    return $key;
            }

        }
    }

    private function refineColumn($parameter,$file,$currentIndex): string
    {
        if (strstr($parameter, ',')) {
            $parameters = explode(",", $parameter);
            $k = $currentIndex +$parameters[0];
            $value = $file[$k-1];

            // explode first parameter
            $explode = explode(':', $parameters[1]);
            $data = explode($explode[1], $value);

            // explode second parameter
            $index = explode(':', $parameters[2]);

            if(strstr($data[$index[1]],'(') || strstr($data[$index[1]],')'))
                return $data[$index[1]+1];
            else
                return $data[$index[1]];
        }else{
           $k = $currentIndex + $parameter;

            return $file[$k-1];
        }

    }

    private function createAmount($amount): float
    {
       $comma = strpos($amount, ',');
        $dot = strpos($amount, '.');


        if (strstr($amount, ',') && strstr($amount, '.'))
        {
            if($dot > $comma)
                return floatval(str_replace(',', '', $amount));
            else
                return floatval(str_replace(['.', ','], ['', '.'], $amount));
        }
        else
            return floatval($amount);
    }

    public function parse()
    {
        $parser_parameters = json_decode($this->vendor->parser_parameters, true);

        try {
            foreach ($this->file as $file) {

                //$htmlFile = file((public_path('finalchange.txt')));
                $htmlFile = file((public_path($file['htmlPath'])));

                $length = count($htmlFile);

                //Saving Invoice
                $invoiceData['user_id'] = $this->user->id;
                $invoiceData['vendor_id'] = $this->vendor->id;
                $invoiceData['slug'] = 'EMPTY-SLUG';
                $invoiceData['pdf_file'] = $file['filePath'];
                $invoiceData['html_file'] = $file['htmlPath'];

                $invoiceData['status'] = 1;
                $invoiceData['title'] = $this->emailSubject;


                // invoice number
                if(isset($parser_parameters['invoice_no'])) {
                    if (strstr($parser_parameters['invoice_no'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['invoice_no'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['invoice_number'] = str_replace($replaceFrom, $replaceTo, $find);
                    } elseif (strstr($parser_parameters['invoice_no'], '-')) {
                        $total = explode("-", $parser_parameters['invoice_no']);
                        $parser_parameters['invoice_no'] = $length - $total[1];
                        $invoiceData['invoice_number'] = $this->findDataFromFile($parser_parameters['invoice_no'], $htmlFile,'value');
                    } else
                        $invoiceData['invoice_number'] = $this->findDataFromFile($parser_parameters['invoice_no'], $htmlFile,'value');

                    $invoiceData['invoice_number'] = strip_tags($invoiceData['invoice_number']);
                    $invoiceData['invoice_number'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['invoice_number']);
                }
                else
                    $invoiceData['invoice_number'] = 'NULL';

                // CVR no
                if(isset($parser_parameters['cvr_number'])) {
                    if (strstr($parser_parameters['cvr_number'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['cvr_number'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['cvr_number'] = str_replace($replaceFrom, $replaceTo,$find);
                    } elseif (strstr($parser_parameters['cvr_number'], '-')) {
                        $total = explode("-", $parser_parameters['cvr_number']);
                        $parser_parameters['cvr_number'] = $length - $total[1];
                        $invoiceData['cvr_number'] = $this->findDataFromFile($parser_parameters['cvr_number'], $htmlFile,'value');
                    } else
                        $invoiceData['cvr_number'] = $this->findDataFromFile($parser_parameters['cvr_number'], $htmlFile,'value');

                    $invoiceData['cvr_number'] = strip_tags($invoiceData['cvr_number']);
                    $invoiceData['cvr_number'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['cvr_number']);
                }
                else
                    $invoiceData['cvr_number'] = 'NULL';

                // Total
                if(isset($parser_parameters['total'])) {
                    if (strstr($parser_parameters['total'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['total'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['total'] = str_replace($replaceFrom, $replaceTo, $find);
                    } elseif (strstr($parser_parameters['total'], '-')) {
                        $total = explode("-", $parser_parameters['total']);
                        $parser_parameters['total'] = $length - $total[1];
                        $invoiceData['total'] = $this->findDataFromFile($parser_parameters['total'], $htmlFile,'value');
                    } else {
                        $invoiceData['total'] = $this->findDataFromFile($parser_parameters['total'], $htmlFile,'value');
                        $invoiceData['total'] = str_replace(['Â DKK', ' '], ['', ''], $invoiceData['total']);
                    }

                    $invoiceData['total'] = str_replace(["\r\n", "\r", "\n", " "], '', strip_tags($invoiceData['total']));

                    if (strpos($invoiceData['total'], '.') > strpos($invoiceData['total'], ',')) {
                        $invoiceData['total'] = floatval(str_replace([','], [''], $invoiceData['total']));
                    } else
                        $invoiceData['total'] = floatval(str_replace(['.', ','], ['', '.'], $invoiceData['total']));
                }
                else
                    $invoiceData['total'] = 'NULL';

                // Vat
                if(isset($parser_parameters['vat'])) {
                    if (strstr($parser_parameters['vat'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['vat'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['vat'] = str_replace($replaceFrom, $replaceTo, $find);
                    } elseif (strstr($parser_parameters['vat'], '-')) {
                        $vat = explode("-", $parser_parameters['vat']);
                        $parser_parameters['vat'] = $length - $vat[1];
                        $invoiceData['vat'] = $this->findDataFromFile($parser_parameters['vat'], $htmlFile,'value');
                    } else{
                        $invoiceData['vat'] = $this->findDataFromFile($parser_parameters['vat'], $htmlFile,'value');
                        $invoiceData['vat'] = str_replace(' ', '', $invoiceData['vat']);
                    }

                    $invoiceData['vat'] = str_replace(["\r\n", "\r", "\n", ' '], "", strip_tags($invoiceData['vat']));

                    if (strpos($invoiceData['vat'], '.') > strpos($invoiceData['vat'], ',')) {
                        $invoiceData['vat'] = floatval(str_replace([','], [''], $invoiceData['vat']));
                    } else
                        $invoiceData['vat'] = floatval(str_replace(['.', ','], ['', '.'], $invoiceData['vat']));
                }
                else
                    $invoiceData['vat'] = 0.0;

                // Sub Total
                if(isset($parser_parameters['sub_total'])) {
                    if (strstr($parser_parameters['sub_total'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['sub_total'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['sub_total'] = str_replace($replaceFrom, $replaceTo, $find);
                    } elseif (strstr($parser_parameters['sub_total'], '-')) {
                        $sub_total = explode("-", $parser_parameters['sub_total']);
                        $parser_parameters['sub_total'] = $length - $sub_total[1];
                        $invoiceData['sub_total'] = $this->findDataFromFile($parser_parameters['sub_total'], $htmlFile,'value');
                    } else
                        $invoiceData['sub_total'] = str_replace(['Â DKK', ' '], '', $this->findDataFromFile($parser_parameters['sub_total'], $htmlFile,'value'));


                    $invoiceData['sub_total'] = str_replace(["\r\n", "\r", "\n", ' '], "", strip_tags($invoiceData['sub_total']));

                    if (strpos($invoiceData['sub_total'], '.') > strpos($invoiceData['sub_total'], ',')) {
                        $invoiceData['sub_total'] = floatval(str_replace([','], [''], $invoiceData['sub_total']));
                    } else
                        $invoiceData['sub_total'] = floatval(str_replace(['.', ','], ['', '.'], $invoiceData['sub_total']));
                }
                else
                    $invoiceData['sub_total'] = 0.0;

                // Grand Total
                if(isset($parser_parameters['grand_total'])) {
                    if (strstr($parser_parameters['grand_total'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['grand_total'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['grand_total'] = str_replace($replaceFrom, $replaceTo, $find);
                    } elseif (strstr($parser_parameters['grand_total'], '-')) {
                        $total = explode("-", $parser_parameters['grand_total']);
                        $parser_parameters['grand_total'] = $length - $total[1];
                        $invoiceData['grand_total'] = $this->findDataFromFile($parser_parameters['grand_total'], $htmlFile,'value');
                    } else
                        $invoiceData['grand_total'] = $this->findDataFromFile($parser_parameters['grand_total'], $htmlFile,'value');

                    $invoiceData['grand_total'] = str_replace(["\r\n", "\r", "\n", 'Â DKK', ' '], "", strip_tags($invoiceData['grand_total']));

                    if (strpos($invoiceData['grand_total'], '.') > strpos($invoiceData['grand_total'], ',')) {
                        $invoiceData['grand_total'] = floatval(str_replace([','], [''], $invoiceData['grand_total']));
                    } else
                        $invoiceData['grand_total'] = floatval(str_replace(['.', ','], ['', '.'], $invoiceData['grand_total']));

                }
                else
                    $invoiceData['grand_total'] = 0.0;

                 //  if($invoiceData['vat'] !== 0.0)
                  //      $invoiceData['vat'] = round(($invoiceData['vat'] / $invoiceData['total']) * 100, 2); //percentage

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
                if(isset($parser_parameters['invoice_date'])) {
                    if (strstr($parser_parameters['invoice_date'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['invoice_date'], $length);
                       $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['invoice_date'] = str_replace($replaceFrom, $replaceTo, $find);
                    } elseif (strstr($parser_parameters['invoice_date'], '-')) {

                        $invoice_date = explode("-", $parser_parameters['invoice_date']);
                        $parser_parameters['invoice_date'] = $length - $invoice_date[1];
                        $invoiceData['invoice_date'] = $this->findDataFromFile($parser_parameters['invoice_date'], $htmlFile,'value');
                    } else
                        $invoiceData['invoice_date'] = $this->findDataFromFile($parser_parameters['invoice_date'], $htmlFile,'value');


                    $invoiceData['invoice_date'] = strip_tags($invoiceData['invoice_date']);
                    $invoiceData['invoice_date'] = str_replace(["\r\n", "\r", "\n"], "", $invoiceData['invoice_date']);
                    $invoiceData['invoice_date'] = str_replace(["­"], "-", $invoiceData['invoice_date']);

                }else
                    $invoiceData['invoice_date'] = 'NULL';

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


                //Companies

                // company name
                if(isset($parser_parameters['company_name'])) {
                    if (strstr($parser_parameters['company_name'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['company_name'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['company_name'] = str_replace($replaceFrom, $replaceTo,$find);
                    } elseif (strstr($parser_parameters['company_name'], '-')) {
                        $total = explode("-", $parser_parameters['company_name']);
                        $parser_parameters['company_name'] = $length - $total[1];
                        $invoiceData['company_name'] = $this->findDataFromFile($parser_parameters['company_name'], $htmlFile,'value');
                    } else
                        $invoiceData['company_name'] = $this->findDataFromFile($parser_parameters['company_name'], $htmlFile,'value');

                    $invoiceData['company_name'] = strip_tags($invoiceData['company_name']);
                    $invoiceData['company_name'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['company_name']);
                }
                else
                    $invoiceData['company_name'] = 'NULL';


                // company email
                if(isset($parser_parameters['company_email'])) {
                    if (strstr($parser_parameters['company_email'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['company_email'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['company_email'] = str_replace($replaceFrom, $replaceTo,$find);
                    } elseif (strstr($parser_parameters['company_email'], '-')) {
                        $total = explode("-", $parser_parameters['company_email']);
                        $parser_parameters['company_email'] = $length - $total[1];
                        $invoiceData['company_email'] = $this->findDataFromFile($parser_parameters['company_email'], $htmlFile,'value');
                    } else
                        $invoiceData['company_email'] = $this->findDataFromFile($parser_parameters['company_email'], $htmlFile,'value');

                    $invoiceData['company_email'] = strip_tags($invoiceData['company_email']);
                    $invoiceData['company_email'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['company_email']);
                }
                else
                    $invoiceData['company_email'] = 'NULL';

                // company phone
                if(isset($parser_parameters['company_phone'])) {
                    if (strstr($parser_parameters['company_phone'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['company_phone'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['company_phone'] = str_replace($replaceFrom, $replaceTo,$find);
                    } elseif (strstr($parser_parameters['company_phone'], '-')) {
                        $total = explode("-", $parser_parameters['company_phone']);
                        $parser_parameters['company_phone'] = $length - $total[1];
                        $invoiceData['company_phone'] = $this->findDataFromFile($parser_parameters['company_phone'], $htmlFile,'value');
                    } else
                        $invoiceData['company_phone'] = $this->findDataFromFile($parser_parameters['company_phone'], $htmlFile,'value');

                    $invoiceData['company_phone'] = strip_tags($invoiceData['company_phone']);
                    $invoiceData['company_phone'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['company_phone']);
                }
                else
                    $invoiceData['company_phone'] = 'NULL';

                // company address
                if(isset($parser_parameters['company_address'])) {
                    if ((strstr($parser_parameters['company_address'], '@') && strstr($parser_parameters['company_address'], '+')) || strstr($parser_parameters['company_address'], '+')) {
                        $invoiceData['company_address'] = $this->multipleIndexConcat($parser_parameters['company_address'],$htmlFile,$length);
                    } elseif (strstr($parser_parameters['company_address'], '-')) {
                        $total = explode("-", $parser_parameters['company_address']);
                        $parser_parameters['company_address'] = $length - $total[1];
                        $invoiceData['company_address'] = $this->findDataFromFile($parser_parameters['company_address'], $htmlFile,'value');
                    }
                    elseif((strstr($parser_parameters['company_address'], '@'))){
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['company_address'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['company_address'] = str_replace($replaceFrom, $replaceTo,$find);
                    }
                    else
                        $invoiceData['company_address'] = $this->findDataFromFile($parser_parameters['company_address'], $htmlFile,'value');

                    $invoiceData['company_address'] = strip_tags($invoiceData['company_address']);
                    $invoiceData['company_address'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['company_address']);
                }
                else
                    $invoiceData['company_address'] = 'NULL';

                // company cvr
                if(isset($parser_parameters['company_cvr'])) {
                    if (strstr($parser_parameters['company_cvr'], '@')) {
                        list($replaceFrom, $replaceTo, $pItem) = $this->stringReplace($parser_parameters['company_cvr'], $length);
                        $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                        $invoiceData['company_cvr'] = str_replace($replaceFrom, $replaceTo,$find);
                    } elseif (strstr($parser_parameters['company_cvr'], '-')) {
                        $total = explode("-", $parser_parameters['company_cvr']);
                        $parser_parameters['company_cvr'] = $length - $total[1];
                        $invoiceData['company_cvr'] = $this->findDataFromFile($parser_parameters['company_cvr'], $htmlFile,'value');
                    } else
                        $invoiceData['company_cvr'] = $this->findDataFromFile($parser_parameters['company_cvr'], $htmlFile,'value');

                    $invoiceData['company_cvr'] = strip_tags($invoiceData['company_cvr']);
                    $invoiceData['company_cvr'] = str_replace(["\r\n", "\r", "\n", ' '], "", $invoiceData['company_cvr']);
                }
                else
                    $invoiceData['company_cvr'] = 'NULL';

                // Product List
                $index = 0;

                //products end
                if (isset($parser_parameters['products_end'])) {
                    if(strstr($parser_parameters['products_end'], '|'))
                        $products_length = $this->findDataFromFile($parser_parameters['products_end'], $htmlFile,'key','|');
                    else
                        $products_length = $this->findDataFromFile($parser_parameters['products_end'], $htmlFile,'key');
                }else
                    $products_length = $length;


                //$products start;
                if (strstr($parser_parameters['products_start'], '|'))
                    $productStart = $this->findDataFromFile($parser_parameters['products_start'], $htmlFile,'key','|');
                else
                    $productStart = $this->findDataFromFile($parser_parameters['products_start'], $htmlFile,'key');


                $products_columns = $parser_parameters['products_columns'];

                if(is_numeric($parser_parameters['products_start']))
                    $i=$productStart;
                else
                    $i=$productStart + 1;


                while($i<$products_length) {

                    $nextLoop = $i;
                    $incrementForI = 0;
                    $product_row_length = $parser_parameters['product_row_length'];
                    $parser_parameters['products_columns'] = $products_columns;

                    for ($j = 1; $j <= $product_row_length; $j++) {


                        $k = $i + $parser_parameters['products_columns']['name'];
                        // echo '==='.$j.'===';

                           if ($j == 1) {
                               if (!isset($parser_parameters['products_end'])) {

                                   $increment = $incrementForI = $this->compareNameWithNextLines($k, $htmlFile);
                                   foreach ($parser_parameters['products_columns'] as $key => $value) {
                                       if ($key !== 'product_no' && $key !== 'name')
                                           $parser_parameters['products_columns'][$key] = $value + $increment;
                                   }

                                   $k = $i + ($parser_parameters['products_columns']['product_total'] - 1);
                                   if (isset($htmlFile[$k])) {
                                       //  echo 'isset';
                                       $total = $htmlFile[$k];
                                       $total = str_replace(["\r\n", "\r", "\n"], "", strip_tags($total));
                                       $total = floatval(str_replace(['.', ',', 'DKK', 'Â', ' '], ['', '.', '', '', ''], ($total)));
                                       if (!$total && (!is_int($total) || !is_float($total))) {
                                           break 2;
                                       }

                                       $k = $i + $parser_parameters['products_columns']['name'];
                                       $productList[$index]['name'] = str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($htmlFile[$k - 1]));
                                       $name = $productList[$index]['name'];
                                       if (strstr($name, 'Stk') || strstr($name, 'stk') || strstr($name, 'krt')) {
                                           $productList[$index]['name'] = str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($htmlFile[$k - 2]));
                                           foreach ($parser_parameters['products_columns'] as $key => $value) {
                                               if ($key !== 'product_no' && $key !== 'name')
                                                   $parser_parameters['products_columns'][$key] = $value - 1;
                                           }
                                           $product_row_length = $product_row_length - 1;
                                       } else
                                           $product_row_length = $product_row_length + $increment;

                                   }
                                   if (!isset($htmlFile[$k]))
                                       break 2;
                               }
                           }
                        //echo '--'.$product_row_length.'--';
                        // $k = $i + $parser_parameters['products_columns']['name'];


                         //echo $j;

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
                             if($result == '')
                                 $productList[$index]['product_no'] = $product_no;
                             else
                                 $productList[$index]['product_no'] = $result;

                         }


                        if (isset($parser_parameters['products_columns']['name']) && $parser_parameters['products_columns']['name'] == $j) {
                            // echo 'name';
                            if (strstr($parser_parameters['products_columns']['name'], ',')) {
                                $productList[$index]['name'] = $this->refineColumn($parser_parameters['products_columns']['name'], $htmlFile, $i);
                                $productList[$index]['name'] = str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($productList[$index]['name']));
                            } else {
                                $k = $i + $j;
                                $pItem = $k -1;
                                $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                                $productList[$index]['name'] = ltrim(str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($find)));

                                if (isset($productList[$index]['product_no']) && $productList[$index]['product_no'] == $productList[$index]['name']) {
                                    //$i++;
                                    $k = $i + $j;
                                    $productList[$index]['name'] = ltrim(str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($htmlFile[$k - 1])));
                                }
                                $name = $productList[$index]['name'];
                                if (strstr($name, 'Stk') || strstr($name, 'stk') || strstr($name, 'krt')) {
                                    $productList[$index]['name'] = str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($htmlFile[$k - 2]));
                                    foreach ($parser_parameters['products_columns'] as $key => $value) {
                                        if ($key !== 'product_no' && $key !== 'name')
                                            $parser_parameters['products_columns'][$key] = $value - 1;
                                    }
                                    $product_row_length = $product_row_length - 1;
                                }
                                $increment = $incrementForI = $this->compareNameWithNextLines($k, $htmlFile);
                                if ($increment > 0) {
                                    $total = $parser_parameters['products_columns']['name'] + $increment;
                                    $k = $i + ($total - 1);
                                    $total = $htmlFile[$k];
                                    $total = str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], ['', '', 'Ø', '', '', '', ''], strip_tags($total));
                                    $productList[$index]['name'] = $productList[$index]['name'] . $total;
                                }
                            }

                        }

                             if (isset($parser_parameters['products_columns']['stock']) && $parser_parameters['products_columns']['stock'] == $j) {
                                 $k = $i + $j;
                                 $productList[$index]['stock'] = str_replace(['.', ',', 'Â '], ['', '.', ''], strip_tags($htmlFile[$k-1]));

                                $productList[$index]['stock']  = str_replace(['.',',','Â',"\r\n","\r","\n",' '], ['','.','','','','',''], strip_tags($productList[$index]['stock']));

                            }
                             if (isset($parser_parameters['products_columns']['content']) && $parser_parameters['products_columns']['content'] == $j) {
                                 $k = $i + $j;
                                 $productList[$index]['content'] = str_replace(['.', ',', 'Â '], ['', '.', ''], strip_tags($htmlFile[$k-1]));

                                $productList[$index]['content']  = str_replace(['.',',','Â',"\r\n","\r","\n",' '], ['','.','','','','',''], strip_tags($productList[$index]['content']));

                            }

                             if (isset($parser_parameters['products_columns']['content_price']) && $parser_parameters['products_columns']['content_price'] == $j) {
                                 $k = $i + $j;
                                 $productList[$index]['content_price'] = str_replace(['.', ',', 'Â '], ['', '.', ''], strip_tags($htmlFile[$k-1]));

                                $productList[$index]['content_price']  = str_replace(['.',',','Â',"\r\n","\r","\n",' '], ['','.','','','','',''], strip_tags($productList[$index]['content_price']));

                            }
                             if (isset($parser_parameters['products_columns']['unit']) && $parser_parameters['products_columns']['unit'] == $j) {
                                 $k = $i + $j;
                                 $productList[$index]['unit'] = str_replace(['.', ',', 'Â '], ['', '.', ''], strip_tags($htmlFile[$k-1]));

                                $productList[$index]['unit']  = str_replace(['.',',','Â',"\r\n","\r","\n",' '], ['','.','','','','',''], strip_tags($productList[$index]['unit']));

                            }

                        if (isset($parser_parameters['products_columns']['qty']) && $parser_parameters['products_columns']['qty'] == $j) {

                            if (strstr($parser_parameters['products_columns']['qty'], ',')) {
                                $productList[$index]['qty'] = $this->refineColumn($parser_parameters['products_columns']['qty'], $htmlFile, $i);
                                $productList[$index]['qty'] = str_replace(['Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n",' '], ['', '', 'Ø', '', '', '', '', ''], strip_tags($productList[$index]['qty']));
                            } else {
                                $k = $i + $j;
                                $pItem = $k -1;
                                $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                                $productList[$index]['qty'] = str_replace(['.', ',', 'Â', "\r\n", "\r", "\n",' ',' '], ['', '.', '', '', '', '', '', ''], strip_tags($find));
                                while (empty($productList[$index]['qty'])) {
                                    $i++;
                                    $k = $i + $j;
                                    $pItem = $k -1;
                                    $find = $this->findDataFromFile($pItem, $htmlFile,'value');
                                    $productList[$index]['qty'] = str_replace(['.', ',', 'Â', "\r\n", "\r", "\n",' '], ['', '.', '', '', '', '', ''], strip_tags($find));
                                }

//                                if (isset($productList[$index]['stock']) && 1 !== preg_match('~[0-9]~', $productList[$index]['stock']))
//                                    $productList[$index]['qty'] = $productList[$index]['qty'] . ' ' . $productList[$index]['stock'];
                            }
                            $productList[$index]['qty'] = preg_replace("/[^0-9.]/", "", $productList[$index]['qty']);
                        }

                        if (isset($parser_parameters['products_columns']['price']) && $parser_parameters['products_columns']['price'] == $j) {
                            if (strstr($parser_parameters['products_columns']['price'], ',')) {
                                $productList[$index]['price'] = $this->refineColumn($parser_parameters['products_columns']['price'], $htmlFile, $i);
                                $productList[$index]['price'] = str_replace(['$','Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n",' '], '', strip_tags($productList[$index]['price']));
                            }
                            else {
                                $k = $i + $j;
                                $pItem = $k - 1;
                                $find = $this->findDataFromFile($pItem, $htmlFile, 'value');
                                $productList[$index]['price'] = str_replace([' ', "\r\n", "\r", "\n"], '', strip_tags($find));

                                while (empty($productList[$index]['price']) || $productList[$index]['price'] == '0.0') {
                                    $i++;
                                    $k = $i + $j;
                                    $productList[$index]['price'] = floatval(str_replace(['Â ', "\r\n", "\r", "\n", ' '], '', strip_tags($htmlFile[$k - 1])));
                                }
                            }

                            $productList[$index]['price'] = $this->createAmount($productList[$index]['price']);
                        }
                        if (isset($parser_parameters['products_columns']['product_total']) && $parser_parameters['products_columns']['product_total'] == $j) {

                            if (strstr($parser_parameters['products_columns']['product_total'], ',')) {
                                $productList[$index]['total'] = $this->refineColumn($parser_parameters['products_columns']['product_total'], $htmlFile, $i);
                                $productList[$index]['total'] = str_replace(['$','Â', 'Ã', 'Ã', '*', "\r\n", "\r", "\n"], '', strip_tags($productList[$index]['total']));
                            }
                            else {

                                $k = $i + $j;
                                $productList[$index]['total'] = str_replace(['DKK', 'Â', ' ', "\r\n", "\r", "\n",], ['', '', '', '', '', ''], strip_tags($htmlFile[$k - 1]));

                                while (empty($productList[$index]['total']) || $productList[$index]['total'] == '0.0') {
                                    $i++;
                                    $k = $i + $j;
                                    $productList[$index]['total'] = str_replace(['Â ', "\r\n", "\r", "\n", ' '], '', strip_tags($htmlFile[$k - 1]));
                                }
                            }

                            $productList[$index]['total'] = $this->createAmount($productList[$index]['total']);

                        }

                        if (isset($productList[$index]['total'])) {
                            $productList[$index]['user_id'] = $this->user->id;
                            $productList[$index]['vendor_id'] = $this->vendor->id;
                            $productList[$index]['sub_total'] = $productList[$index]['total'];
                            $productList[$index]['grand_total'] = $productList[$index]['total'];
                        }
                    }
                        $i = $nextLoop + $product_row_length;
                        $index++;

                }
                $createNewService = new CreateCompleteInvoiceService($invoiceData,$this->vendor,$productList);
                $createInvoice = $createNewService->create();

                if($createInvoice == False)
                    Throw new Exception();
            }

            return true;
        }catch (Throwable $e) {
            return '(Line No. '.$e->getLine().'): '.$e->getMessage();
        }
    }


}
