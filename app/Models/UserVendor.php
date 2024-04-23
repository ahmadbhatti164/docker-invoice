<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVendor extends Model
{
    use SoftDeletes;
    public function users(){
        return $this->belongsTo(User::class);
    }

    public function vendors(){
        return $this->belongsToMany(Vendor::class);
    }
}
