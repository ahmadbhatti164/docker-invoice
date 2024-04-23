<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    //

    protected $fillable = ['title','user_id','vendor_id','currency_id','company_id','shipping_address','billing_address','total','discount','sub_total','slug','grand_total','vat','pdf_file','html_file','invoice_number','invoice_date','cvr_number','status','balance'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function vendor(){
    	return $this->belongsTo(Vendor::class);
    }

    public function currency(){
    	return $this->belongsTo(Currency::class);
    }
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class,'invoice_products')->withPivot(['total','price','qty','discount','sub_total','vat','grand_total']);
    }

}
