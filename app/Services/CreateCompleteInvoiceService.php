<?php


namespace App\Services;


use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CreateCompleteInvoiceService {

    private $invoice;
    private $vendor;
    private $products;

    public function __construct($invoice, $vendor, $products) {
        $this->invoice = $invoice;
        $this->vendor = $vendor;
        $this->products = $products;
    }

    public function create(): bool
    {
    try {
        $this->invoice['company_id'] = $this->findAndCreateCompany($this->invoice);

        $invoice = Invoice::create($this->invoice);

        $invoice->slug = Str::slug($invoice->title . "-" . $invoice->id);
        $invoice->save();

        // Add user vendor
        $results = DB::select('select id FROM user_vendors where user_id = "' . $invoice->user_id . '" AND vendor_id = "' . $this->vendor['id'] . '" ');
        if (count($results) == 0) {
           // DB::insert('insert into user_vendors (user_id, vendor_id) values (' . $invoice->user_id . ', ' . $this->vendor['id'] . ')');
            $user = User::findOrFail($invoice->user_id);
            $user->vendors()->attach($this->vendor['id']);
        }

        # Add Invoice with products
        try {

            foreach ($this->products as $pv) {
                $product = Product::select('id')->where(['name'=>$pv['name'],'vendor_id'=>$this->vendor['id']])->first();

                $addProduct['name'] = $pv['name'];
                $addProduct['product_no'] = isset($pv['product_no'])? $pv['product_no'] : '';
                $addProduct['vendor_id'] = $pv['vendor_id'];
                $addProduct['user_id'] = $invoice->user_id;
                $addProduct['status'] = 1;
                $addProduct['content'] = isset($pv['content'])? $pv['content'] : '';
                $addProduct['content_price'] = isset($pv['content_price'])? $pv['content_price'] : '';
                $addProduct['unit'] = isset($pv['unit'])? $pv['unit'] : '';
                $addProduct['slug'] = 'EMPTY-SLUG';
                $addProduct['price'] = $pv['price'];


                $invoiceProduct['invoice_id'] = $invoice->id;
                $invoiceProduct['qty'] = isset($pv['qty'])? $pv['qty'] : 0;
                $invoiceProduct['price'] = $pv['price'];
                $invoiceProduct['total'] = $pv['total'];
                $invoiceProduct['discount'] = isset($pv['discount'])? $pv['discount'] : 0;
                $invoiceProduct['sub_total'] = isset($pv['sub_total'])? $pv['sub_total'] : 0;
                $invoiceProduct['vat'] = isset($pv['vat'])? $pv['vat'] : 0;
                $invoiceProduct['grand_total'] = $pv['grand_total'];

                if(!$product)
                {
                    $product = Product::create($addProduct);
                    $product->slug = Str::slug($product->name . "-" . $product->id);
                    $product->save();
                    $invoiceProduct['product_id'] = $product->id;
                }
                else
                    $invoiceProduct['product_id'] = $product->id;

                InvoiceProduct::create($invoiceProduct);
            }

             }catch (Throwable $e) {
                //Invoice::findOrFail($invoice->id)->delete();
                return false;
            }

        return true;
        }catch (Throwable $e) {
        dd($e);
            return false;
        }
    }

    private function findAndCreateCompany($invoice)
    {
        $company = Company::query();
        if (isset($invoice['company_name'])) {
            $company = $company->where('name','=',$invoice['company_name'])->first();
        }
        elseif(isset($invoice['company_email'])) {
            $company = $company->where('email','=',$invoice['company_email'])->first();
        }

        if($company) {
            $company_id = $company['id'];
            $company = CompanyUser::where(['user_id'=>$invoice['user_id'],'company_id'=>$company_id])->first();
            if(empty($company)){
                $user = User::find($invoice['user_id']);
                $user->companies()->attach($company_id);
            }

        }
        else {

            if ($invoice['company_name'] == 'NULL' && $invoice['company_email'] == 'NULL')
                $company_id = null;
            else
            {
                if (isset($invoice['company_name']) && $invoice['company_name'] != 'NULL')
                    $addCompany['name'] = $invoice['company_name'];
                else
                    $addCompany['name'] = $invoice['email'];

                if (isset($invoice['company_email']) && $invoice['company_email'] != 'NULL')
                    $addCompany['email'] = $invoice['company_email'];
                else
                    $addCompany['email'] = $addCompany['name'];

                $addCompany['cvr_number'] = $invoice['company_cvr'];
                $addCompany['phone_no'] = $invoice['company_phone'];
                $addCompany['address'] = $invoice['company_address'];
                $addCompany['status'] = 1;
                $addCompany['slug'] = 'EMPTY-SLUG';
                $company = Company::create($addCompany);
                $company->slug = Str::slug($company->name . "-" . $company->id);
                $company->save();
                $user = User::find($invoice['user_id']);
                $user->companies()->attach($company->id);
                $company_id = $company->id;
            }

        }

        return $company_id;

    }
}
