<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vendor extends Model
{
    use SoftDeletes;
    //
    protected $fillable = ['name','email','parser','phone_no','address','parser_parameters','slug','status','category_id','cvr_number'];

    public function invoices(){
    	return $this->hasMany(Invoice::class,'vendor_id');
    }
    public function products(){
    	return $this->hasMany(Product::class);
    }
    public function invoiceProducts(){
    	return $this->hasManyThrough(InvoiceProduct::class,Invoice::class);
    }
    public function category(){
    	return $this->belongsTo(Category::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'user_vendors','vendor_id','user_id');
    }


}
