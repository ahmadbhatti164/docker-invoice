<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceParsingIssue extends Model
{
    protected $fillable = ['user_id','email_title','vendor_id','user_email','vendor_email','pdf_file','html_file','is_new','comments'];

}
