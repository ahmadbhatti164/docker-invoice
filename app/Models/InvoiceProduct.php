<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceProduct extends Model
{
    use SoftDeletes;
    //
    protected  $table = 'invoice_products';
    protected $fillable = ['invoice_id','vendor_id','product_id','price','total','discount','sub_total','vat','grand_total','qty'];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function invoice(){
        return $this->belongsToMany(Invoice::class);
    }
    public function product(){
        return $this->belongsToMany(Product::class);
    }

    public function invoiceVendor()
    {
        return $this->hasManyThrough(Vendor::class, Invoice::class);
    }
}
