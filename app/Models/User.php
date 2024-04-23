<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Invoice;

class User extends Authenticatable
{
    use SoftDeletes;
    //
    use Notifiable; //,HasApiTokens
    protected $fillable = ['name','password','email','phone_no','address','country','state','city','status','slug','is_admin','is_verified'];

    public function invoices(){
    	return $this->hasMany(Invoice::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class,'user_vendors','user_id','vendor_id');
    }
    public function companies()
    {
        return $this->belongsToMany(Company::class,'company_users','user_id','company_id');
    }

    public function isAdmin(  ) {
        return $this->is_admin ? true : false;
    }
}
