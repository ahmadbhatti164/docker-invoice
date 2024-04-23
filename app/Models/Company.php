<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Company extends Model
{
    use SoftDeletes;
    //
    protected $fillable = ['name','email','phone_no','address','slug','status','cvr_number'];

    public function invoices(){
        return $this->hasMany(Invoice::class,'company_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'company_users','company_id','user_id');
    }


}
