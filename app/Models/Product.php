<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    //
    protected $fillable = ['user_id','vendor_id','name','price','total','discount','sub_total','vat','grand_total','qty','slug','status','product_no','content','content_price','unit'];

    public function vendor(){
    	return $this->belongsTo(Vendor::class);
    }

    /*public function invoice(){
    	return $this->belongsTo(Invoice::class);
    }*/
    public function invoiceProduct(){
    	return $this->hasMany(InvoiceProduct::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }



}
