<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class GlobalSearchService extends BaseService {

    public function search($value,$limit = null) {
        $response = collect();

        //Admin
        if (auth()->user()->is_admin === 1) {
            $vendors = Vendor::query();
        } else {
            $vendors = auth()->user()->vendors();
        }

        if(isset($limit))
            $vendors->limit($limit);

        $invoices = $vendors->with([
                    'invoices' => function ($vendors) use ($value) {
                        $vendors->where('title', 'like', '%' . $value . '%');
                        $vendors->Orwhere('invoice_number', 'like', '%' . $value . '%');
                    }
                ])->get();

        foreach ($invoices as $vendor) {
            foreach ($vendor->invoices as $invoice) {
                $response->push([
                    'id' => $invoice->id,
                    'name' => $invoice->title,
                    'vendor' => $vendor->name,
                    'type' => 'invoice'
                ]);
            }
        }
        // Vendors
        $vendorList = $vendors->where(function($query) use($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                            ->orWhere('email', 'like', '%' . $value . '%')
                            ->orWhere('phone_no', 'like', '%' . $value . '%')
                            ->orWhere('cvr_number', 'like', '%' . $value . '%');
                })->get();


        foreach ($vendorList as $vendor) {
            $response->push([
                'id' => $vendor->id,
                'name' => $vendor->name,
                'vendor' => $vendor->name,
                'type' => 'vendor'
            ]);
        }

        //products
        if (auth()->user()->is_admin == 1)
            $product = Product::with('vendor');
        else
            $product = Product::whereId(Auth::user()->id)->with('vendor');

        if(isset($limit))
            $product->limit($limit);


        $productList = $product
                ->where('name', 'like', '%' . $value . '%')
                ->Orwhere('product_no', 'like', '%' . $value . '%')
            ->get();



        if (isset($productList)) {
            //$vendors = $vendors->where('name', 'like', '%'.$value.'%')->orWhere('product_no', 'like', '%'.$value.'%')->get();
            foreach ($productList as $product) {
                $response->push([
                    'id' => $product->id,
                    'name' => $product->name,
                    'vendor' => $product->vendor->name,
                    'type' => 'product'
                ]);
            }
        }

        return $response;
    }

}
