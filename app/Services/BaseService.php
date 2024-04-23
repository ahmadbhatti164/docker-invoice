<?php

namespace App\Services;

use Illuminate\Foundation\Validation\ValidatesRequests;

class BaseService
{
    use ValidatesRequests;

    public static function make()
    {
        return new static();
    }
}
