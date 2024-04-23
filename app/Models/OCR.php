<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OCR extends Model
{
    protected $table = 'ocrs';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
