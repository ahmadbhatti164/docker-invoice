<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyUser extends Model
{
    use SoftDeletes;
    public function users(){
        return $this->belongsTo(User::class);
    }

    public function companies(){
        return $this->belongsToMany(Company::class);
    }
}
